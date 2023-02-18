<?php

namespace App\Service;

use App\Builder\WeatherBuilder;
use App\Client\YandexClient;
use App\Entity\Weather;
use App\Repository\WeatherRepository;
use GuzzleHttp\Exception\GuzzleException;

class WeatherService
{
    private YandexClient $yandexClient;
    private WeatherRepository $weatherRepository;

    public function __construct(YandexClient $yandexClient, WeatherRepository $weatherRepository)
    {
        $this->yandexClient = $yandexClient;
        $this->weatherRepository = $weatherRepository;
    }

    /**
     * @throws GuzzleException
     */
    public function getWeatherByLatLon(float $lat, float $lon): Weather
    {
        // Валидация
        $weatherFromDb = $this->weatherRepository->findOneBy(['lat' => $lat, 'lon' => $lon]);

        if ($weatherFromDb instanceof Weather) { //спрашиваем $weatherFromDb объект Weather??

            return $weatherFromDb;
        }
        $yandexResponse = $this->yandexClient->checkWeather($lat, $lon);

        $weather = WeatherBuilder::createByYandexResponse($yandexResponse); //способ вызова статичных методов

        $this->weatherRepository->save($weather,true);

        return $weather;
    }
}