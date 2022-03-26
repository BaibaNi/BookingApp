<?php
namespace App\Services\Reservation\Reserve;

class ReserveReservationRequest
{
    private int $apartmentId;
    private string $reservedFrom;
    private string $reservedUntil;
    private int $userId;

    public function __construct(int $apartmentId, string $reservedFrom, string $reservedUntil, int $userId)
    {
        $this->apartmentId = $apartmentId;
        $this->reservedFrom = $reservedFrom;
        $this->reservedUntil = $reservedUntil;
        $this->userId = $userId;
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

    public function getUserId(): int
    {
        return $this->userId;
    }
}
