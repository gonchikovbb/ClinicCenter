<?php

namespace App\Entity;

use App\Repository\RecordRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: RecordRepository::class), Table(name: 'records')]
class Record
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[ManyToOne(targetEntity: Doctor::class)]
    #[JoinColumn(name: 'doctor_id', referencedColumnName: 'id')]
    private Doctor $doctor;

    #[Column(type: 'string', unique: true, nullable: false)]
    private string $date;

    #[Column(type: 'string', unique: true, nullable: false)]
    private string $time;

    public function __construct(
        User $user,
        Doctor $doctor,
        string $date,
        string $time)
    {
        $this->user = $user;
        $this->doctor = $doctor;
        $this->date = $date;
        $this->time = $time;
    }
    public function getUser(): User
    {
        return $this->user;
    }
    public function getDoctor(): Doctor
    {
        return $this->doctor;
    }
    public function getDate(): string
    {
        return $this->date;
    }
    public function getTime(): string
    {
        return $this->time;
    }
    public function setDate(string $date)
    {
        $this->date = $date;
    }
    public function setTime(string $time)
    {
        $this->time = $time;
    }
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'time' => $this->time,
            'user' => $this->user->toArray(),
            'doctor' => $this->doctor->toArray()
        ];
    }
}