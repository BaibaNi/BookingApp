<?php
declare(strict_types=1);

namespace Tests;

use App\Models\Apartment;
use PHPUnit\Framework\TestCase;

class ApartmentTest extends TestCase
{
    public function testGetTitle(): void
    {
        $apartment = new Apartment('Test title', 'Test address', 'Test description', 20.0,
        '21-03-2022', '22-03-2022', 3, 2, 4.0);
        $this->assertSame('Test title', $apartment->getTitle());
    }


    public function testGetAddress(): void
    {
        $apartment = new Apartment('Test title', 'Test address', 'Test description', 20.0,
            '21-03-2022', '22-03-2022', 3, 2, 4.0);
        $this->assertSame('Test address', $apartment->getAddress());
    }

    public function testGetDescription(): void
    {
        $apartment = new Apartment('Test title', 'Test address', 'Test description', 20.0,
            '21-03-2022', '22-03-2022', 3, 2, 4.0);
        $this->assertSame('Test description', $apartment->getDescription());
    }

    public function testGetPrice(): void
    {
        $apartment = new Apartment('Test title', 'Test address', 'Test description', 20.0,
            '21-03-2022', '22-03-2022', 3, 2, 4.0);
        $this->assertSame(20.0, $apartment->getPrice());
    }

    public function testGetAvailableFrom(): void
    {
        $apartment = new Apartment('Test title', 'Test address', 'Test description', 20.0,
            '21-03-2022', '22-03-2022', 3, 2, 4.0);
        $this->assertSame('21-03-2022', $apartment->getAvailableFrom());
    }

    public function testGetAvailableUntil(): void
    {
        $apartment = new Apartment('Test title', 'Test address', 'Test description', 20.0,
            '21-03-2022', '22-03-2022', 3, 2, 4.0);
        $this->assertSame('22-03-2022', $apartment->getAvailableUntil());
    }

    public function testGetId(): void
    {
        $apartment = new Apartment('Test title', 'Test address', 'Test description', 20.0,
            '21-03-2022', '22-03-2022', 3, 2, 4.0);
        $this->assertSame(3, $apartment->getId());
    }

    public function testGetUserId(): void
    {
        $apartment = new Apartment('Test title', 'Test address', 'Test description', 20.0,
            '21-03-2022', '22-03-2022', 3, 2, 4.0);
        $this->assertSame(2, $apartment->getUserId());
    }

    public function testGetAvgRating(): void
    {
        $apartment = new Apartment('Test title', 'Test address', 'Test description', 20.0,
            '21-03-2022', '22-03-2022', 3, 2, 4.0);
        $this->assertSame(4.0, $apartment->getAvgRating());
    }

}