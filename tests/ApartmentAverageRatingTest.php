<?php

namespace Tests;

use App\Models\ApartmentAverageRating;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class ApartmentAverageRatingTest extends TestCase
{
    public function testGetApartmentId(): void
    {
        $rating = new ApartmentAverageRating(4, 2.4);
        $this->assertSame(4, $rating->getApartmentId());
    }

    public function testGetAverageRating(): void
    {
        $rating = new ApartmentAverageRating(4, 2.4);
        $this->assertSame(2.4, $rating->getAverageRating());
    }
}
