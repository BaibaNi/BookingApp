<?php
namespace App\Models;

class ApartmentAverageRating
{
    private int $apartmentId;
    private float $averageRating;

    public function __construct(int $apartmentId, float $averageRating)
    {
        $this->apartmentId = $apartmentId;
        $this->averageRating = $averageRating;
    }


    public function getApartmentId(): int
    {
        return $this->apartmentId;
    }


    public function getAverageRating(): float
    {
        return $this->averageRating;
    }
}