<?php
namespace App\Services\Apartment\Create;

use App\Models\Apartment;
use App\Repositories\Apartment\ApartmentRepository;
use App\Repositories\Apartment\DbalApartmentRepository;

class CreateApartmentService
{
    private ApartmentRepository $apartmentRepository;

    public function __construct()
    {
        $this->apartmentRepository = new DbalApartmentRepository();
    }

    public function execute(CreateApartmentRequest $request): Apartment
    {
        $apartment = new Apartment(
            $request->getTitle(),
            $request->getAddress(),
            $request->getDescription(),
            $request->getPrice(),
            $request->getAvailableFrom(),
            $request->getAvailableUntil(),
            $request->getId(),
            $request->getUserId(),
            5
        );

        $this->apartmentRepository->create($apartment);

        return $apartment;
    }
}