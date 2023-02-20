<?php

namespace App\Entity;

use App\Repository\WeatherRepository;
use DateTime;
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
    #[Column(name: 'date_time', type: 'datetime', nullable: false)]
    private DateTime $date_time;
    #[Column(name: 'week', type: 'string', nullable: false)]
    private string $week;
    #[Column(name: 'city', type: 'string', nullable: false)]
    private string $city;
    #[Column(name: 'temp', type: 'integer', nullable: false)]
    private int $temp;
    #[Column(name: 'lat', type: 'float', nullable: false)]
    private float $lat;
    #[Column(name: 'lon', type: 'float', nullable: false)]
    private float $lon;

    public function __construct(
        DateTime $date_time,
        string $week,
        string $city,
        int $temp,
        float $lat,
        float $lon)
    {
       $this->date_time = $date_time;
       $this->week = $week;
       $this->city = $city;
       $this->temp = $temp;
       $this->lat = $lat;
       $this->lon = $lon;
    }

    public function getDateTime(): DateTime
    {
        return $this->date_time;
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

    public function setDateTime(DateTime $date_time)
    {
        $this->date_time = $date_time;
    }

    public function setWeek(string $week)
    {
        $this->week = $week;
    }

    public function setCity(string $city)
    {
        $this->city = $city;
    }

    public function setTemp(int $temp)
    {
        $this->temp = $temp;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'date' => $this->date_time,
            'week' => $this->week,
            'city' => $this->city,
            'temp' => $this->temp,
            'lat' => $this->lat,
            'lon' => $this->lon
        ];
    }
}
