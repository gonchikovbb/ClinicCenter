<?php

namespace App\Client;

use App\Exception\YandexClientException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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
    public function checkWeather(float $lat, float $lon): array
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
        $response = $this->client->request('GET', '', $queryRequest);
        if ($response->getStatusCode() !== 200) {
            throw new YandexClientException('Получен код, не 200. Response body: '. $response->getBody()->getContents());
        }
        return json_decode($response->getBody()->getContents(),true);
    }
}