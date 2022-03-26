<?php
namespace App\Services\Apartment\Review;

class ReviewApartmentRequest
{
    private int $apartmentId;
    private int $userId;
    private string $review;
    private int $rating;

    public function __construct(int $apartmentId, int $userId, string $review, int $rating)
    {

        $this->apartmentId = $apartmentId;
        $this->userId = $userId;
        $this->review = $review;
        $this->rating = $rating;
    }

    public function getApartmentId(): int
    {
        return $this->apartmentId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getReview(): string
    {
        return $this->review;
    }

    public function getRating(): int
    {
        return $this->rating;
    }
}