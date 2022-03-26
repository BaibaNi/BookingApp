<?php
namespace App\Services\User\Show;

use App\Models\UserProfile;

class ShowUserResponse
{

    private UserProfile $userProfile;
    private array $userListedApartments;
    private array $reservedApartments;
    private array $reservations;

    public function __construct(UserProfile $userProfile, array $userListedApartments, array $reservedApartments, array $reservations)
    {
        $this->userProfile = $userProfile;
        $this->userListedApartments = $userListedApartments;
        $this->reservedApartments = $reservedApartments;
        $this->reservations = $reservations;
    }


    public function getUserProfile(): UserProfile
    {
        return $this->userProfile;
    }

    public function getUserListedApartments(): array
    {
        return $this->userListedApartments;
    }

    public function getReservedApartments(): array
    {
        return $this->reservedApartments;
    }

    public function getReservations(): array
    {
        return $this->reservations;
    }

}