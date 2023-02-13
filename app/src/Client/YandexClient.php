<?php

namespace App\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class YandexClient
{
    protected Client $client;
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.weather.yandex.ru/v2/forecast',
            'headers' => [
                'X-Yandex-API-Key'  =>  '544fe4da-26f5-49c9-b44f-8ffeeec7555e'
            ]
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function checkWeather(string $lat, string $lon): ResponseInterface
    {
        $queryRequest= [
            'query' => [
                'lat' => $lat,
                'lon' => $lon,
                'limit' => '1',
                'hours' => 'false',
                'extra' => 'false',
            ]
        ];
        return $this->client->request('GET', '', $queryRequest);
    }
}