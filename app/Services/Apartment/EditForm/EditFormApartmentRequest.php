<?php
namespace App\Services\Apartment\EditForm;

class EditFormApartmentRequest
{
    private int $apartmentId;

    public function __construct(int $apartmentId)
    {
        $this->apartmentId = $apartmentId;
    }

    public function getApartmentId(): int
    {
        return $this->apartmentId;
    }
}