<?php

namespace App\Services\Apartment\Delete;

use App\Database;
use App\Repositories\Apartment\ApartmentRepository;
use App\Repositories\Apartment\DbalApartmentRepository;

class DeleteApartmentService
{

    private ApartmentRepository $apartmentRepository;

    public function __construct()
    {
        $this->apartmentRepository = new DbalApartmentRepository();
    }

    public function execute(DeleteApartmentRequest $request): void
    {
        $apartmentId = $request->getApartmentId();
        $this->apartmentRepository->delete($apartmentId);
    }

    public function check(DeleteApartmentRequest $request): array
    {
        $apartmentId = $request->getApartmentId();
        $reservationList = $this->apartmentRepository->getReservationsById($apartmentId);
        return $reservationList;
    }
}