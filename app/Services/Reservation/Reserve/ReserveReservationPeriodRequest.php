<?php
namespace App\Services\Reservation\Reserve;

class ReserveReservationPeriodRequest
{
    private int $apartmentId;
    private string $reservedFrom;
    private string $reservedUntil;

    public function __construct(int $apartmentId, string $reservedFrom, string $reservedUntil)
    {
        $this->apartmentId = $apartmentId;
        $this->reservedFrom = $reservedFrom;
        $this->reservedUntil = $reservedUntil;
    }

    public function getApartmentId(): int
    {
        return $this->apartmentId;
    }

    public function getReservedFrom(): string
    {
        return $this->reservedFrom;
    }

    public function getReservedUntil(): string
    {
        return $this->reservedUntil;
    }
}