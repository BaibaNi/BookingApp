<?php
namespace App\Models;

class ApartmentReservation
{
    private string $reservedFrom;
    private string $reservedUntil;
    private string $email;
    private ?int $id;
    private int $userId;
    private int $apartmentId;

    public function __construct(string $reservedFrom, string $reservedUntil, string $email, ?int $id, int $userId, int $apartmentId)
    {
        $this->reservedFrom = $reservedFrom;
        $this->reservedUntil = $reservedUntil;
        $this->email = $email;
        $this->id = $id;
        $this->userId = $userId;
        $this->apartmentId = $apartmentId;
    }

    public function getReservedFrom(): string
    {
        return $this->reservedFrom;
    }

    public function getReservedUntil(): string
    {
        return $this->reservedUntil;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getApartmentId(): int
    {
        return $this->apartmentId;
    }
}