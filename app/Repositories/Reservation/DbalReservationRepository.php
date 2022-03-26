<?php
namespace App\Repositories\Reservation;

use App\Database;
use App\Models\ApartmentReservation;

class DbalReservationRepository implements ReservationRepository
{
    /**
     * RESERVATIONS LIST
     */
    public function getReservationsById(int $apartmentId): array
    {
        $reservedApartmentsQuery = Database::connection()
            ->prepare('SELECT * from apartment_reservations where apartment_id = ? order by reserved_from asc ');
        $reservedApartmentsQuery->bindValue(1, $apartmentId);

        return $reservedApartmentsQuery
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function getReservationUserEmails(int $userId): string
    {
        $usersEmailsQuery = Database::connection()
            ->prepare('SELECT email FROM users where id = ?');
        $usersEmailsQuery->bindValue(1, $userId);

        return $usersEmailsQuery
            ->executeQuery()
            ->fetchOne();

    }

    /**
     * RESERVE
    */
    public function getApartmentById(int $apartmentId): array
    {
        $apartmentQuery = Database::connection()
            ->prepare('SELECT * FROM apartments where id = ?');
        $apartmentQuery->bindValue(1, $apartmentId);

        return $apartmentQuery
            ->executeQuery()
            ->fetchAssociative();
    }

    public function getReservationsInPeriodById(int $apartmentId, string $reservedFrom, string $reservedUntil): array
    {
        $reservationsInPeriod = Database::connection()
            ->prepare('SELECT * from apartment_reservations where apartment_id = ?
                           and (reserved_from >= ? and reserved_until <= ?)');
        $reservationsInPeriod->bindValue(1, $apartmentId);
        $reservationsInPeriod->bindValue(2, $reservedFrom);
        $reservationsInPeriod->bindValue(3, $reservedUntil);

        return $reservationsInPeriod
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function reserve(ApartmentReservation $reservation): void
    {
        Database::connection()
            ->insert('apartment_reservations', [
                'reserved_from' => $reservation->getReservedFrom(),
                'reserved_until' => $reservation->getReservedUntil(),
                'apartment_id' => $reservation->getApartmentId(),
                'user_id' => $reservation->getUserId()
            ]);
    }
}