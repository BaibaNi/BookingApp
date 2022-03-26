<?php
namespace App\Services\User\Login;

use App\Repositories\User\DbalUserRepository;
use App\Repositories\User\UserRepository;

class LoginUserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new DbalUserRepository();
    }

    public function getUser(LoginUserEmailRequest $request): array
    {
        return $this->userRepository->getUserByEmail($request->getEmail());

    }

    public function execute(LoginUserIdRequest $request): array
    {
        return $this->userRepository->getUserProfileById($request->getUserId());

    }
}