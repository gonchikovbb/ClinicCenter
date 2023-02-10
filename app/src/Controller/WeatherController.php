<?php

namespace App\Controller;

use App\Client\YandexClient;
use Doctrine\Common\Collections\Expr\Value;
use GuzzleHttp\Exception\GuzzleException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class WeatherController
{
    private YandexClient $yandexClient;
    public function __construct(YandexClient $yandexClient)
    {
        $this->yandexClient = $yandexClient;
    }
    /**
     * @throws GuzzleException
     */
    public function getWeather(Request $request, Response $response, $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $lat = $data['lat'];
        $lon = $data['lon'];

        $yandexResponse = $this->yandexClient->checkWeather($lat, $lon);

        $response->getBody()->write($yandexResponse->getBody()->getContents());

        return $response;
    }

    /**
     * @throws GuzzleException
     */
    public function getWeathers(Request $request, Response $response, $args){

        $data = json_decode($request->getBody()->getContents(), true);

        foreach ($data as $arrayOne){
            foreach ($arrayOne as $keyTwo => $arrayTwo){
                if ($keyTwo ='lat'){
                    $lat = $arrayOne['lat'];
                }
                if ($keyTwo ='lon'){
                    $lon = $arrayOne['lon'];
                }
                $yandexResponseOne = $this->yandexClient->checkWeather($lat, $lon)->getBody()->getContents();
            }
            $yandexResponse[] = json_decode($yandexResponseOne);
        }

        $response->getBody()->write(json_encode($yandexResponse));

        return $response;
    }
}