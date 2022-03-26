<?php
namespace App\Services\Reservation\Reserve;

class ReserveReservationApartmentsRequest
{
    private int $apartmentId;

    public function __construct(int $apartmentId)
    {
        $this->apartmentId = $apartmentId;
    }

    public function getApartmentId(): int
    {
        return $this->apartmentId;
    }
}