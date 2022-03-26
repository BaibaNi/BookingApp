<?php
namespace App\Services\Apartment\Edit;

use App\Repositories\Apartment\ApartmentRepository;
use App\Repositories\Apartment\DbalApartmentRepository;

class EditApartmentService
{

    private ApartmentRepository $apartmentRepository;

    public function __construct()
    {
        $this->apartmentRepository = new DbalApartmentRepository();
    }

    public function execute(EditApartmentRequest $request): array
    {

        $apartmentParams = [
            'id' => $request->getId(),
            'title' => $request->getTitle(),
            'address' => $request->getAddress(),
            'description' => $request->getDescription(),
            'price' => $request->getPrice()
        ];

        $this->apartmentRepository->edit($apartmentParams);

        return $apartmentParams;
    }
}