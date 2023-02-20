<?php

namespace App\Builder;

use App\Entity\Weather;
use DateTime;

class WeatherBuilder
{
    public static function createByYandexResponse(array $yandexResponse):Weather
    {
        $date_time = new DateTime('now');
        $week = $yandexResponse['forecasts']['0']['week'];
        $city = $yandexResponse['geo_object']['locality']['name'];
        $temp = $yandexResponse['fact']['temp'];
        $lat = $yandexResponse['info']['lat'];
        $lon = $yandexResponse['info']['lon'];
        switch ($week) {
            case 1:
                $week = 'Понедельник';
                break;
            case 2:
                $week = 'Вторник';
                break;
            case 3:
                $week = 'Среда';
                break;
            case 4:
                $week = 'Четверг';
                break;
            case 5:
                $week = 'Пятница';
                break;
            case 6:
                $week = 'Суббота';
                break;
            case 7:
                $week = 'Воскресенье';
                break;
        }
        return new Weather($date_time,$week,$city,$temp,$lat,$lon);
    }
}