<?php
namespace App\Services\User\Index;

use App\Models\User;
use App\Repositories\User\DbalUserRepository;
use App\Repositories\User\UserRepository;

class IndexUserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new DbalUserRepository();
    }

    public function execute(): array
    {
        $usersList = $this->userRepository->getUsers();

        $users = [];
        foreach ($usersList as $user){
            $users[] = new User(
                $user['email'],
                $user['password'],
                $user['created_at'],
                $user['id']
            );
        }

        return $users;
    }
}