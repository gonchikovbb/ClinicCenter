<?php

namespace App\Entity;

use App\Repository\DoctorRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: DoctorRepository::class), Table(name: 'doctors')]
class Doctor
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;
    #[Column(name: 'specialization',type: 'string', nullable: false)]
    private string $specialization;
    #[Column(name: 'experience',type: 'string', nullable: false)]
    private string $experience;
    #[Column(name: 'education',type: 'string', nullable: false)]
    private string $education;
    #[Column(name: 'clinics',type: 'string')]
    private ?string $clinics;
    #[Column(name: 'instagram',type: 'string')]
    private ?string $instagram;
    #[Column(name: 'vk',type: 'string')]
    private ?string $vk;

    public function __construct(

        User $user,
        string $specialization,
        string $experience,
        string $education,
        string $clinics = null,
        string $instagram = null,
        string $vk = null
)
    {
        $this->user = $user;
        $this->specialization = $specialization;
        $this->experience = $experience;
        $this->education = $education;
        $this->clinics = $clinics;
        $this->instagram = $instagram;
        $this->vk = $vk;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getSpecialization(): string
    {
        return $this->specialization;
    }
    public function getExperience(): string
    {
        return $this->experience;
    }
    public function getEducation(): string
    {
        return $this->education;
    }
    public function getClinics(): string
    {
        return $this->clinics;
    }
    public function getInstagram(): string
    {
        return $this->instagram;
    }
    public function getVk(): string
    {
        return $this->vk;
    }

    public function setSpecialization(string $specialization)
    {
        $this->specialization = $specialization;
    }
    public function setExperience(string $experience)
    {
        $this->experience = $experience;
    }
    public function setEducation(string $education)
    {
        $this->education = $education;
    }
    public function setClinics(string $clinics)
    {
        $this->clinics = $clinics;
    }
    public function setInstagram(string $instagram)
    {
        $this->instagram = $instagram;
    }
    public function setVk(string $vk)
    {
        $this->vk = $vk;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'specialization' => $this->specialization,
            'experience' => $this->experience,
            'education' => $this->education,
            'clinics' => $this->clinics,
            'instagram' => $this->instagram,
            'vk' => $this->vk,
            'user_id' => $this->user->toArray()
        ];
    }
}