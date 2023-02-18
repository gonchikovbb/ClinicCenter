<?php

namespace App\Controller;

use App\Service\WeatherService;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use GuzzleHttp\Exception\GuzzleException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class WeatherController
{
    private Connection $connection;
    private WeatherService $weatherService;
    public function __construct(Connection $connection, WeatherService $weatherService)
    {
        $this->connection = $connection;
        $this->weatherService = $weatherService;
    }

    /**
     * @throws GuzzleException
     */
    public function getWeather(Request $request, Response $response, $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $lat = $data['lat'];
        $lon = $data['lon'];

        $weather = $this->weatherService->getWeatherByLatLon($lat, $lon);

        $response->getBody()->write(json_encode($weather->toArray()));

        return $response;
    }

    /**
     * @throws Exception|GuzzleException
     */
    public function getWeathers(Request $request, Response $response, $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        //Валидация
        $weathers = [];

        $this->connection->beginTransaction();
        try {
            foreach ($data as $coordinate) {

                $lat = $coordinate['lat'];
                $lon = $coordinate['lon'];

                $weathers[] = $this->weatherService->getWeatherByLatLon($lat, $lon)->toArray();
            }
            $response->getBody()->write(json_encode($weathers));

            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
        return $response;
    }
}