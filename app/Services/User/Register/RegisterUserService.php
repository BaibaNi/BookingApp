<?php
namespace App\Services\User\Register;

use App\Models\User;
use App\Models\UserProfile;
use App\Repositories\User\DbalUserRepository;
use App\Repositories\User\UserRepository;
use Carbon\Carbon;

class RegisterUserService
{

    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new DbalUserRepository();
    }

    public function execute(RegisterUserRequest $request): void
    {
        $user = new User(
            $request->getEmail(),
            $request->getPassword(),
            Carbon::now()->format('Y-m-d')
        );

        $this->userRepository->storeUser($user);

        $lastInsertedUser = $this->userRepository->getLastInsertedUser();

        $userProfile = new UserProfile(
            $request->getName(),
            $request->getSurname(),
            $request->getBirthday(),
            $request->getEmail(),
            $request->getPassword(),
            Carbon::now()->format('Y-m-d'),
            $lastInsertedUser['id']
        );

        $this->userRepository->registerUserProfile($userProfile);

    }

}