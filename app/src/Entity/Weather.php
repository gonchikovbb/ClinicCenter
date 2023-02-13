<?php

namespace App\Entity;

use App\Repository\WeatherRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: WeatherRepository::class), Table(name: 'weather')]
class Weather
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;
    #[Column(name: 'date', type: 'string', nullable: false)]
    private string $date;
    #[Column(name: 'week', type: 'string', nullable: false)]
    private string $week;
    #[Column(name: 'city', type: 'string', nullable: false)]
    private string $city;
    #[Column(name: 'temp', type: 'string', nullable: false)]
    private string $temp;
    #[Column(name: 'lat', type: 'string', nullable: false)]
    private string $lat;
    #[Column(name: 'lon', type: 'string', nullable: false)]
    private string $lon;


    public function __construct(string $date, string $week, string $city, string $temp, string $lat, string $lon)
    {
       $this->date = $date;
       $this->week = $week;
       $this->city = $city;
       $this->temp = $temp;
       $this->lat = $lat;
       $this->lon = $lon;
    }

    public function getDate():string
    {
        return $this->date;
    }
    public function getWeek():string
    {
        return $this->week;
    }
    public function getCity():string
    {
        return $this->city;
    }
    public function getTemp():string
    {
        return $this->temp;
    }
    public function getLat():string
    {
        return $this->lat;
    }
    public function getLon():string
    {
        return $this->lon;
    }
    public function setDate(string $date)
    {
        $this->date = $date;
    }
    public function setWeek(string $week)
    {
        $this->week = $week;
    }
    public function setCity(string $city)
    {
        $this->city = $city;
    }
    public function setTemp(string $temp)
    {
        $this->temp = $temp;
    }
    public function toArray()
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'week' => $this->week,
            'city' => $this->city,
            'temp' => $this->temp,
            'lat' => $this->lat,
            'lon' => $this->lon
        ];
    }
}
