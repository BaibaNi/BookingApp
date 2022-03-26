<?php
declare(strict_types=1);

namespace Tests;

use App\Models\ApartmentReservation;
use PHPUnit\Framework\TestCase;

class ApartmentReservationTest extends TestCase
{
    public function testGetReservedFrom(): void
    {
        $reservation = new ApartmentReservation('2022-03-22', '2022-03-28', 'email@email.com', 2, 4, 115);
        $this->assertSame('2022-03-22', $reservation->getReservedFrom());
    }

    public function testGetReservedUntil(): void
    {
        $reservation = new ApartmentReservation('2022-03-22', '2022-03-28', 'email@email.com', 2, 4, 115);
        $this->assertSame('2022-03-28', $reservation->getReservedUntil());
    }

    public function testGetEmail(): void
    {
        $reservation = new ApartmentReservation('2022-03-22', '2022-03-28', 'email@email.com', 2, 4, 115);
        $this->assertSame('email@email.com', $reservation->getEmail());
    }

    public function testGetId(): void
    {
        $reservation = new ApartmentReservation('2022-03-22', '2022-03-28', 'email@email.com', 2, 4, 115);
        $this->assertSame(2, $reservation->getId());
    }

    public function testGetUserId(): void
    {
        $reservation = new ApartmentReservation('2022-03-22', '2022-03-28', 'email@email.com', 2, 4, 115);
        $this->assertSame(4, $reservation->getUserId());
    }

    public function testGetApartmentId(): void
    {
        $reservation = new ApartmentReservation('2022-03-22', '2022-03-28', 'email@email.com', 2, 4, 115);
        $this->assertSame(115, $reservation->getApartmentId());
    }
}