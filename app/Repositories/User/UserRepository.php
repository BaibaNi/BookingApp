<?php
namespace App\Repositories\User;

use App\Models\User;
use App\Models\UserProfile;

interface UserRepository
{
    /**
     * USER LIST
    */
    public function getUsers(): array;

    /**
     * SHOW USER
     */
    public function getUserById(int $userId): array;
    public function getUserProfileById(int $userId): array;
    public function getListedApartmentsById(int $userId): array;
    public function getApartmentAvgRatingById(int $apartmentId): float;
    public function getReservedApartmentsById(int $userId): array;
    public function getReservationsById(int $userId): array;


    /**
     * USER REGISTRATION
     */
    public function storeUser(User $user): void;
    public function getLastInsertedUser(): array;
    public function registerUserProfile(UserProfile $userProfile): void;


    /**
     * USER LOGIN
     */
    public function getUserByEmail(string $userEmail): array;
}