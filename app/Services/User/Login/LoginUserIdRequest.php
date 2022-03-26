<?php
namespace App\Services\User\Login;

class LoginUserIdRequest
{

    private int $userId;

    public function __construct(int $userId)
    {

        $this->userId = $userId;
    }


    public function getUserId(): int
    {
        return $this->userId;
    }
}