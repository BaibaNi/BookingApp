<?php
namespace App\Services\Apartment\Show;


use App\Models\Apartment;

class ShowApartmentResponse
{
    private Apartment $apartment;
    private array $reviews;
    private array $reservationDates;
    private float $totalPrice;


    public function __construct(Apartment $apartment, array $reviews, array $reservationDates, float $totalPrice)
    {
        $this->apartment = $apartment;
        $this->reviews = $reviews;
        $this->reservationDates = $reservationDates;
        $this->totalPrice = $totalPrice;
    }

    public function getApartment(): Apartment
    {
        return $this->apartment;
    }

    public function getReviews(): array
    {
        return $this->reviews;
    }

    public function getReservationDates(): array
    {
        return $this->reservationDates;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

}