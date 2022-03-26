<?php
namespace App\Repositories\Reservation;


use App\Models\ApartmentReservation;

interface ReservationRepository
{
    /**
     * RESERVATIONS LIST
    */
    public function getReservationsById(int $apartmentId): array;
    public function getReservationUserEmails(int $userId): string;

    /**
     * RESERVE
    */
    public function getApartmentById(int $apartmentId): array;
    public function getReservationsInPeriodById(int $apartmentId, string $reservedFrom, string $reservedUntil): array;
    public function reserve(ApartmentReservation $reservation): void;
}

