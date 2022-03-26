<?php
namespace App\Services\Reservation\Index;

use App\Models\ApartmentReservation;
use App\Repositories\Reservation\DbalReservationRepository;
use App\Repositories\Reservation\ReservationRepository;

class IndexReservationService
{
    private ReservationRepository $reservationRepository;

    public function __construct()
    {
        $this->reservationRepository = new DbalReservationRepository();
    }

    public function execute(IndexReservationRequest $request): array
    {
        $apartmentId = $request->getApartmentId();

        $reservationsList = $this->reservationRepository->getReservationsById($apartmentId);

        $reservations = [];

        foreach ($reservationsList as $reservation) {

            $userEmail = $this->reservationRepository->getReservationUserEmails($reservation['user_id']);

            $reservations[] = new ApartmentReservation(
                $reservation['reserved_from'],
                $reservation['reserved_until'],
                $userEmail,
                $reservation['id'],
                $reservation['user_id'],
                $reservation['apartment_id']
            );
        }

        return $reservations;
    }
}