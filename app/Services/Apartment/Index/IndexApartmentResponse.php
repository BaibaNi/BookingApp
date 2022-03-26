<?php
namespace App\Services\Apartment\Index;

class IndexApartmentResponse
{
    private array $apartments;

    public function __construct(array $apartments)
    {
        $this->apartments = $apartments;
    }

    public function getApartments(): array
    {
        return $this->apartments;
    }
}