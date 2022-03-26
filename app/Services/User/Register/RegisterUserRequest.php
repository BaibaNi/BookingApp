<?php
namespace App\Services\User\Register;

class RegisterUserRequest
{
    private string $email;
    private string $password;
    private string $name;
    private string $surname;
    private string $birthday;

    public function __construct(string $email, string $password, string $name, string $surname, string $birthday)
    {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
        $this->birthday = $birthday;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getBirthday(): string
    {
        return $this->birthday;
    }
}