# OpenWeatherMap API Application in PHP

<div>
    <img src='https://img.shields.io/packagist/dependency-v/scrnr/weather/php?label=PHP&logo=php&style=plastic&color=orange' alt='PHP Version'>
    <img src='https://img.shields.io/packagist/v/scrnr/weather?label=Packagist&logo=packagist&logoColor=white&style=plastic' alt='Packagist Version'>
    <img src='https://img.shields.io/packagist/l/scrnr/weather?label=LICENSE&style=plastic' alt='LICENSE'>
</div>

<br>

## Table Of Contects

* [Description](#description)
* [Installation](#installation)
* [How to use](#how-to-use)
* [Example](#for-example)
* [Features](#features)
* [Requirements](#requirements)
* [Author](#author)
* [License](#license)

## Description

This repository written in PHP that allows you to check the weather for a particular location using the OpenWeatherMap API 2.5. The app can determine your location by IP address and show the weather information or you can enter the name of any city.

## Installation

You can install the library using [Composer](https://getcomposer.org/). Simply add the following lines to your `composer.json` file and run `composer install`:

```json
"require": {
    "scrnr/weather": "*"
}
```

Or you can use this **command**:

```bash
composer require scrnr/weather
```

## How to use

To use this app, you'll need to obtain an **API key** from the [OpenWeatherMap website](https://openweathermap.org/api) and set it using the `setToken()` function. The `setToken()` function accepts a token in string format.

Once you've set your **API key**, you can retrieve weather data using the `getWeather()` function. This function accepts two parameters: `CITY_NAME` and `UNITS_OF_MEASUREMENT`.

* `CITY_NAME` accepts the name of the city in string format
* `UNITS_OF_MEASUREMENT` accepts either `Units::METRIC` or `Units::IMPERIAL` as constants to specify the units of measurement.

If you do not set these parameters, the function will automatically determine the city based on the *IP address* and use the *metric system* as the *default* units of measurement.

If the `getWeather()` function returns false, you can use the `getErrors()` function to retrieve information about the error. This will allow you to troubleshoot any issues that may have occurred during the process.

Thank you for using my OpenWeatherMap API Application!

## For example:

```php
use Scrnr\Weather\Weather;

Weather::setToken('YOUR_TOKEN');
$forecast = Weather::getWeather('CITY_NAME', 'UNITS');

if ($forecast === false) {
    $errors = Weather::getErrors();
}
```

### Output

```php
Array
(
    [today] => Array
        (
            [sunrise] => 05:09
            [sunset] => 20:43
            [date] => 27 April
            [description] => Overcast clouds
            [temp] => 5
            [feelsLike] => 1
            [pressure] => 755 mm Hg
            [humidity] => 90%
            [cityName] => Saint Petersburg
            [visibility] => more than 10 km
            [wind] => Array
                (
                    [speed] => 7 m/s
                    [direction] => East
                )

            [isNight] => false
            [icon] => http://openweathermap.org/img/wn/04d@2x.png
            [tempMin] => 4
        )
        
    [fourDays] => Array
        (
            [0] => Array
                (
                    [date] => 28 April
                    [icon] => http://openweathermap.org/img/wn/04d@2x.png
                    [max] => 7
                    [min] => 3
                )

            [1] => Array
                (
                    [date] => 29 April
                    [icon] => http://openweathermap.org/img/wn/10d@2x.png
                    [max] => 4
                    [min] => 4
                )

            [2] => Array
                (
                    [date] => 30 April
                    [icon] => http://openweathermap.org/img/wn/10d@2x.png
                    [max] => 7
                    [min] => 4
                )

            [3] => Array
                (
                    [date] => 01 May
                    [icon] => http://openweathermap.org/img/wn/10d@2x.png
                    [max] => 7
                    [min] => 2
                )
        )
)
```

## Features

This app offers the following features:

* Ability to check weather for a specific city
* Automatic location detection using IP address
* Display of current weather conditions, temperature, humidity, and wind speed
* Display of a 4-day weather forecast

## Requirements

This app requires `PHP 8.0` or later and the `cURL` and `json` extensions to be installed on your web server.

## Author
👤 GitHub: [scrnr](https://github.com/scrnr)

## License

This project is licensed under the MIT License. See the [LICENSE](https://github.com/scrnr/OpenWeatherApi-PHP/blob/main/LICENSE) file for details.
