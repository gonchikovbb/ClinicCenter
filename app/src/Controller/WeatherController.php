<?php

namespace App\Controller;

use App\Builder\WeatherBuilder;
use App\Client\YandexClient;
use App\Entity\Weather;
use App\Repository\WeatherRepository;
use Doctrine\Common\Collections\Expr\Value;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class WeatherController
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
    public function getWeather(Request $request, Response $response, $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        // Валидация
        $lat = $data['lat'];
        $lon = $data['lon'];

        $weatherFromDb = $this->weatherRepository->findOneBy(['lat' => $lat, 'lon' => $lon]);
        //print_r($weatherFromDb);die;

        if (!empty($weatherFromDb)) {

            /** @var Weather $weatherFromDb*/
            $weatherFromDb = $weatherFromDb->toArray();

            $response->getBody()->write(json_encode($weatherFromDb));

            return $response;
        }
        $yandexResponse = $this->yandexClient->checkWeather($lat, $lon);

        $weather = WeatherBuilder::createByYandexResponse($yandexResponse); //способ вызова статичных методов

        $this->weatherRepository->save($weather,true);

        $weather = $weather->toArray();

        $response->getBody()->write(json_encode($weather));

        return $response;
    }

    /**
     * @throws GuzzleException
     */
    public function getWeathers(Request $request, Response $response, $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        //Валидация
        $weathers = [];

        //add transactions
        foreach ($data as $coordinate) {

            $lat = $coordinate['lat'];
            $lon = $coordinate['lon'];

            $weatherFromDb = $this->weatherRepository->findOneBy(['lat' => $lat, 'lon' => $lon]);

            if (!empty($weatherFromDb)) {

                /** @var Weather $weatherFromDb*/
                $weatherFromDb = $weatherFromDb->toArray();

                $response->getBody()->write(json_encode($weatherFromDb));

            } else {
                $yandexResponse = $this->yandexClient->checkWeather($lat, $lon);

                $weather = WeatherBuilder::createByYandexResponse($yandexResponse); //способ вызова статичных методов

                $this->weatherRepository->save($weather,true);

                $weather = $weather->toArray();

                $weathers[] = $weather;

                $response->getBody()->write(json_encode($weathers));
            }
        }
        return $response;
    }
}