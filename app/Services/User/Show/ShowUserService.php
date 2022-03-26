<?php
namespace App\Services\User\Show;

use App\Models\Apartment;
use App\Models\ApartmentReservation;
use App\Models\UserProfile;
use App\Repositories\User\DbalUserRepository;
use App\Repositories\User\UserRepository;

class ShowUserService
{

    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new DbalUserRepository();
    }

    public function execute(ShowUserRequest $request): ShowUserResponse
    {
        /**
         * USER PROFILE
        */
        $userId = $request->getUserId();

        $userInfo = $this->userRepository->getUserById($userId);
        $userProfileInfo = $this->userRepository->getUserProfileById($userId);

        $userProfile = new UserProfile(
            $userProfileInfo['name'],
            $userProfileInfo['surname'],
            $userProfileInfo['birthday'],
            $userInfo['email'],
            $userInfo['password'],
            $userInfo['created_at'],
            $userInfo['id']
        );


        /**
         * USER LISTED APARTMENTS
        */
        $apartmentInfo = $this->userRepository->getListedApartmentsById($userId);

        $userListedApartments = [];
        foreach ($apartmentInfo as $apartment){

            $apartmentAvgRating = $this->userRepository->getApartmentAvgRatingById($apartment['id']);

            $userListedApartments[] = new Apartment(
                $apartment['title'],
                $apartment['address'],
                $apartment['description'],
                $apartment['price'],
                $apartment['available_from'],
                $apartment['available_until'],
                $apartment['id'],
                $apartment['user_id'],
                $apartmentAvgRating
            );
        }


        /**
         * USER RESERVED APARTMENTS
        */

        $reservedApartmentsInfo = $this->userRepository->getReservedApartmentsById($userId);

        $reservedApartments = [];
        foreach ($reservedApartmentsInfo as $reservation){

            $apartmentAvgRating = $this->userRepository->getApartmentAvgRatingById($reservation['id']);

            $reservedApartments[] = new Apartment(
                $reservation['title'],
                $reservation['address'],
                $reservation['description'],
                $reservation['price'],
                $reservation['reserved_from'],
                $reservation['reserved_until'],
                $reservation['id'],
                $reservation['user_id'],
                $apartmentAvgRating
            );
        }


        /**
         * RESERVATIONS OF LISTED APARTMENT
        */

        $reservationsInfo = $this->userRepository->getReservationsById($userId);

        $reservations =[];
        foreach ($reservationsInfo as $reservation) {

            $reservations[] = new ApartmentReservation(
                $reservation['reserved_from'],
                $reservation['reserved_until'],
                $reservation['email'],
                $reservation['id'],
                $reservation['user_id'],
                $reservation['apartment_id']
            );
        }

        return new ShowUserResponse(
            $userProfile,
            $userListedApartments,
            $reservedApartments,
            $reservations
        );
    }
}
