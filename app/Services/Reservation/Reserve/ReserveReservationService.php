<?php
namespace App\Services\Reservation\Reserve;

use App\Models\ApartmentReservation;
use App\Repositories\Reservation\DbalReservationRepository;
use App\Repositories\Reservation\ReservationRepository;

class ReserveReservationService
{

    private ReservationRepository $reservationRepository;

    public function __construct()
    {
        $this->reservationRepository = new DbalReservationRepository();
    }
    public function checkApartment(ReserveReservationApartmentsRequest $request): array
    {
        $apartmentId = $request->getApartmentId();
        $apartmentInfo = $this->reservationRepository->getApartmentById($apartmentId);
        return $apartmentInfo;
    }

    public function checkReservations(ReserveReservationPeriodRequest $request): array
    {
        $reservationsInPeriod = $this->reservationRepository->getReservationsInPeriodById(
            $request->getApartmentId(),
            $request->getReservedFrom(),
            $request->getReservedUntil()
        );

        return $reservationsInPeriod;
    }

    public function execute(ReserveReservationRequest $request): void
    {
        $reservation = new ApartmentReservation(
            $request->getReservedFrom(),
            $request->getReservedUntil(),
            '',
            0,
            $request->getUserId(),
            $request->getApartmentId()
        );

        $this->reservationRepository->reserve($reservation);
    }
}