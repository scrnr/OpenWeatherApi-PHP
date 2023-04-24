# OpenWeatherMap API Application in PHP

This repository written in PHP that allows you to check the weather for a particular location using the OpenWeatherMap API 2.5. The app can determine your location by IP address and show the weather information, or you can enter the name of any city yourself.

## How to use

To use this app, you'll need to obtain an API key from the [OpenWeatherMap website](https://openweathermap.org/api) and set it using the `setToken()` function. The `setToken()` function accepts a token in string format.

Once you've set your API key, you can retrieve weather data using the `getWeather()` function. This function accepts two parameters: CITY_NAME and UNITS_OF_MEASUREMENT.

* CITY_NAME accepts the name of the city in string format
* UNITS_OF_MEASUREMENT accepts either `Units::METRIC` or `Units::IMPERIAL` as constants to specify the units of measurement.

If you do not set these parameters, the function will automatically determine the city based on the IP address and use the metric system as the default units of measurement.

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

## Features

This app offers the following features:

* Ability to check weather for a specific city
* Automatic location detection using IP address
* Display of current weather conditions, temperature, humidity, and wind speed
* Display of a 4-day weather forecast

## Requirements

This app requires PHP 8.0 or later and the cURL extension to be installed on your web server.

## Author
👤 GitHub: [scrnr](https://github.com/scrnr)

## License

This project is licensed under the MIT License. See the [LICENSE](https://github.com/scrnr/OpenWeatherApi-PHP/blob/main/LICENSE) file for details.
