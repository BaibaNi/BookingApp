<?php
namespace App\Services\Reservation\Index;

class IndexReservationRequest
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