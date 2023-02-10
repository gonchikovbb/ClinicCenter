<?php

namespace App\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class YandexClient
{
    protected Client $client;
    protected array $yaKey;
    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://api.weather.yandex.ru/v2/forecast']);
        $this->yaKey = ['X-Yandex-API-Key'  =>  '544fe4da-26f5-49c9-b44f-8ffeeec7555e'];
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
            ],
            'headers' =>  $this->yaKey
        ];
        return $this->client->request('GET', '', $queryRequest);
    }

    //get
//    public function checkWeathers(string $lat, string $lon)
//    {
//        $queryRequest= [
//            'query' => [
//                'lat' => $lat,
//                'lon' => $lon,
//                'limit' => '1',
//                'hours' => 'false',
//                'extra' => 'false',
//            ],
//            'headers' => [
//                'X-Yandex-API-Key'  =>  '544fe4da-26f5-49c9-b44f-8ffeeec7555e'
//            ]
//        ];
//        $yandexResponse = $this->client->request('GET', '', $queryRequest);
//
//        return $yandexResponse;
//    }
}