<?php

declare(strict_types=1);

namespace Scrnr\Weather;

final class Weather
{
    private static ?Weather $instance = null;

    private const URI_IP = 'http://api.sypexgeo.net/json/';
    private const URI_CURRENT = 'https://api.openweathermap.org/data/2.5/weather?';
    private const URI_5_DAYS = 'https://api.openweathermap.org/data/2.5/forecast?';

    private const ERROR_CITY_NAME = 'Please enter a valid city name';

    private array $errors = [];
    private bool $isSecondRequest = false;
    private int $timezone;
    private string $token;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function setToken(string $token): void
    {
        self::getInstance()->token = $token;
    }

    public static function getWeather(string $cityName = '', string $units = Units::METRIC): array|false
    {
        $self = self::getInstance();

        if (empty(trim($cityName))) {
            $cityName = $self->getCityName();
        } else {
            $cityName = urldecode($cityName);
        }

        if ($self->emptyCityName($cityName)) {
            return false;
        }

        $headers = [
            'q' => $cityName,
            'appid' => $self->token,
            'units' => $units,
            'lang' => 'en'
        ];

        $dataToday = null;
        $dataForecast = null;

        $result = $self->tryRequest(self::URI_CURRENT, $headers, $dataToday);

        if ($result === false) {
            if ($self->isSecondRequest) {
                return $self->isSecondRequest = false;
            } else {
                $cityName = $self->getCityName();

                if ($self->emptyCityName($cityName)) {
                    return false;
                }

                $self->addErrors(self::ERROR_CITY_NAME);
                $self->isSecondRequest = true;

                $forecast = $self->getWeather($cityName, $units);

                if ($forecast === false) {
                    $self->isSecondRequest = false;
                }

                return $forecast;
            }
        }

        $self->tryRequest(self::URI_5_DAYS, $headers, $dataForecast);
        $today = $self->getCurrentForecast($dataToday, $units);
        $forecast = $self->getForecastFor4Days($dataForecast->list, $today);

        return [
            'today' => $today,
            'fourDays' => $forecast
        ];
    }

    public static function getErrors(): array
    {
        return self::getInstance()->errors;
    }

    private function emptyCityName(string $cityName): bool
    {
        if (empty(trim($cityName))) {
            $this->addErrors(self::ERROR_CITY_NAME);

            return true;
        }

        return false;
    }

    private function getCurrentForecast(object $data, string $units): array
    {
        $icon = $data->weather[0]->icon;
        $this->timezone = $data->timezone;
        $currentTime = $data->dt + $this->timezone;
        $sunriseTime = $data->sys->sunrise + $this->timezone;
        $sunsetTime = $data->sys->sunset + $this->timezone;

        $forecast = [
            'sunrise' => date('H:i', $sunriseTime),
            'sunset' => date('H:i', $sunsetTime),
            'date' => date('d F', $currentTime),
            'description' => ucfirst($data->weather[0]->description),
            'temp' => round($data->main->temp),
            'feelsLike' => round($data->main->feels_like),
            'pressure' => round($data->main->pressure * 0.75) . ' mm Hg',
            'humidity' => $data->main->humidity . '%',
            'cityName' => $data->name,
            'visibility' => $this->getVisibility($data->visibility, $units),
            'wind' => $this->getWind($data->wind, $units),
            'isNight' => str_contains($icon, 'n'),
            'icon' => $this->getIcon($icon)
        ];

        return $forecast;
    }

    private function getForecastFor4Days(array $data, array &$todayForecast): array
    {
        $forecast = [];

        for ($i = 2; $i < 6; $i++) {
            $timeNight = strtotime("+{$i} day", strtotime($todayForecast['date']));
            $timeDay = strtotime('-12 hours', $timeNight);

            $dayInfo = [];

            foreach ($data as $day) {
                $time = $day->dt + $this->timezone;

                if ($time >= strtotime('-1 day', $timeNight - 3600) and $time <= strtotime('-1 day', $timeNight + 3600)) {
                    $todayForecast['tempMin'] = round($day->main->temp);
                }

                if ($time >= $timeDay - 7200 and $time <= $timeDay + 7200) {
                    $dayInfo[] = [
                        'date' => date('d F', $time),
                        'icon' => $this->getIcon($day->weather[0]->icon),
                        'max' => round($day->main->temp)
                    ];
                }

                if ($time >= $timeNight - 7200 and $time <= $timeNight + 7200) {
                    $dayInfo[] = [
                        'min' => round($day->main->temp)
                    ];
                }
            }

            $forecast[] = array_merge(array_shift($dayInfo), array_pop($dayInfo));
        }

        return $forecast;
    }

    private function getWind(object $wind, string $units): array
    {
        $direction = '';
        $degrees = $wind->deg;
        $speed = round($wind->speed);

        if ($units === Units::IMPERIAL) {
            $speed = (string) $speed . ' mph';
        } else {
            $speed = (string) $speed . ' m/s';
        }

        if ($degrees >= 22 and $degrees < 68) {
            $direction = 'South-West';
        } elseif ($degrees >= 68 and $degrees < 113) {
            $direction = 'West';
        } elseif ($degrees >= 113 and $degrees < 158) {
            $direction = 'North-West';
        } elseif ($degrees >= 158 and $degrees < 203) {
            $direction = 'North';
        } elseif ($degrees >= 203 and $degrees < 248) {
            $direction = 'North-East';
        } elseif ($degrees >= 248 and $degrees < 293) {
            $direction = 'East';
        } elseif ($degrees >= 293 and $degrees < 338) {
            $direction = 'South-East';
        } else {
            $direction = 'South';
        }

        return [
            'speed' => $speed,
            'direction' => $direction
        ];
    }

    private function getVisibility(int $metres, string $units): string
    {
        $visibility = [];
        $km = round($metres / 1000, 1);
        $mi = $this->kmToMi($km);

        if ($km === 10.0) {
            $visibility[] = 'more than';
        }

        if ($units === Units::IMPERIAL) {
            $visibility[] = $mi;
            $visibility[] = 'mi';
        } else {
            $visibility[] = $km;
            $visibility[] = 'km';
        }

        return join(' ', $visibility);
    }

    private function getIcon(string $icon): string
    {
        return "http://openweathermap.org/img/wn/{$icon}@2x.png";
    }

    private function kmToMi(float $km): float
    {
        return round($km / 1.609, 1);
    }

    private function getCityName(): string
    {
        $userInfo = null;
        $result = $this->tryRequest(self::URI_IP, [], $userInfo);

        if ($result === false) {
            $this->addErrors('Error retrieving information about the user');

            return '';
        }

        return $userInfo->city->name_en;
    }

    private function addErrors(string $error): void
    {
        $this->errors[] = $error;
    }

    private function tryRequest(string $url, array $headers, mixed &$data): bool
    {
        $ch = curl_init($url . http_build_query($headers));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code !== 200) {
            return false;
        }

        $data = json_decode($json);

        return true;
    }

    private static function getInstance(): self
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        return self::$instance = new self;
    }
}
