<?php
declare(strict_types=1);

namespace Tests;

use App\Models\ApartmentReview;
use PHPUnit\Framework\TestCase;

class ApartmentReviewTest extends TestCase
{
    public function testGetName(): void
    {
        $review = new ApartmentReview('TestName', 'TestSurname', 'Very good.', 4, '2022-02-27', 59, 5);
        $this->assertSame('TestName', $review->getName());
    }

    public function testGetSurname(): void
    {
        $review = new ApartmentReview('TestName', 'TestSurname', 'Very good.', 4, '2022-02-27', 59, 5);
        $this->assertSame('TestSurname', $review->getSurname());
    }

    public function testGetReview(): void
    {
        $review = new ApartmentReview('TestName', 'TestSurname', 'Very good.', 4, '2022-02-27', 59, 5);
        $this->assertSame('Very good.', $review->getReview());
    }

    public function testGetRating(): void
    {
        $review = new ApartmentReview('TestName', 'TestSurname', 'Very good.', 4, '2022-02-27', 59, 5);
        $this->assertSame(4, $review->getRating());
    }

    public function testGetCreatedAt(): void
    {
        $review = new ApartmentReview('TestName', 'TestSurname', 'Very good.', 4, '2022-02-27', 59, 5);
        $this->assertSame('2022-02-27', $review->getCreatedAt());
    }

    public function testGetId(): void
    {
        $review = new ApartmentReview('TestName', 'TestSurname', 'Very good.', 4, '2022-02-27', 59, 5);
        $this->assertSame(59, $review->getId());
    }

    public function testGetApartmentId(): void
    {
        $review = new ApartmentReview('TestName', 'TestSurname', 'Very good.', 4, '2022-02-27', 59, 5);
        $this->assertSame(5, $review->getApartmentId());
    }

}
