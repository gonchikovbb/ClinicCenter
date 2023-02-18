<?php

use App\Builder\WeatherBuilder;
use App\Client\YandexClient;
use App\Entity\Weather;
use App\Repository\WeatherRepository;
use Doctrine\ORM\EntityManager;
use UMA\DIC\Container;

/** @var Container $container */
$container = require __DIR__ . '/../../config/bootstrap.php';

$em = $container->get(EntityManager::class);

/** @var WeatherRepository $weatherRepository */
$weatherRepository = $em->getRepository(Weather::class);

/** @var YandexClient $yandexClient */
$yandexClient = $container->get(YandexClient::class);

/** @var Weather[] $weathers */
$weathers = $weatherRepository->findAll();

foreach ($weathers as $row) {

    $lat = $row->getLat();
    $lon = $row->getLon();

    $yandexResponse = $yandexClient->checkWeather($lat, $lon);

    $weather = WeatherBuilder::createByYandexResponse($yandexResponse); //способ вызова статичных методов

    $row->setDateTime($weather->getDateTime());
    $row->setTemp($weather->getTemp());
    $row->setWeek($weather->getWeek());

    $weatherRepository->save($row,true);
}

echo 'Обновлено: '.count($weathers)."\n";