<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: UserRepository::class), Table(name: 'users')]
class User
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;
    #[Column(name: 'first_name',type: 'string', nullable: false)]
    private string $firstName;
    #[Column(name: 'second_name',type: 'string', nullable: false)]
    private string $secondName;
    #[Column(type: 'string', nullable: false)]
    private string $gender;

    #[Column(type: 'string', unique: true, nullable: false)]
    private string $email;
    #[Column(type: 'string', nullable: false)]
    private string $password;
    #[Column(type: 'string', unique: true, nullable: false)]
    private string $phone;
    #[ManyToOne(targetEntity: Role::class)]
    #[JoinColumn(name: 'role_id', referencedColumnName: 'id')]
    private Role $role;
    #[Column(name: 'third_name',type: 'string')]
    private ?string $thirdName;
    #[Column(name: 'token',type: 'string')]
    private ?string $token;

    public function __construct(
        string $firstName,
        string $secondName,
        string $gender,
        string $email,
        string $password,
        string $phone,
        Role $role,
        string $thirdName = null,
        string $token = null)
    {
        $this->firstName = $firstName;
        $this->secondName = $secondName;
        $this->gender = $gender;
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
        $this->role = $role;
        $this->thirdName = $thirdName;
        $this->token = $token;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getSecondName(): string
    {
        return $this->secondName;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getThirdName(): string
    {
        return $this->thirdName;
    }
    public function getPhone(): string
    {
        return $this->phone;
    }
    public function getToken(): string
    {
        return $this->token;
    }
    public function getRole(): Role
    {
        return $this->role;
    }

    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }
    public function setSecondName(string $secondName)
    {
        $this->secondName = $secondName;
    }
    public function setThirdName(string $thirdName)
    {
        $this->thirdName = $thirdName;
    }
    public function setEmail(string $email)
    {
        $this->email = $email;
    }
    public function setPassword(string $password)
    {
        $this->password = $password;
    }
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }
    public function setGender(string $gender)
    {
        $this->gender = $gender;
    }
    public function setToken(string $token)
    {
         $this->token = $token;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'second_name' => $this->secondName,
            'gender' => $this->gender,
            'third_name' => $this->thirdName,
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
            'token' => $this->token,
            'role' => $this->role->toArray()
        ];
    }
}