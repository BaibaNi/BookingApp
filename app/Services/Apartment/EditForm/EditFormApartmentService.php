<?php
namespace App\Services\Apartment\EditForm;

use App\Models\Apartment;
use App\Repositories\Apartment\ApartmentRepository;
use App\Repositories\Apartment\DbalApartmentRepository;

class EditFormApartmentService
{

    private ApartmentRepository $apartmentRepository;

    public function __construct()
    {
        $this->apartmentRepository = new DbalApartmentRepository();
    }

    public function execute(EditFormApartmentRequest $request): Apartment
    {
        $apartmentId = $request->getApartmentId();
        $selectedApartment = $this->apartmentRepository->getApartmentById($apartmentId);
        $apartmentAvgRating = $this->apartmentRepository->getApartmentAvgRatingById($apartmentId);

        return new Apartment(
            $selectedApartment['title'],
            $selectedApartment['address'],
            $selectedApartment['description'],
            $selectedApartment['price'],
            $selectedApartment['available_from'],
            $selectedApartment['available_until'],
            $selectedApartment['id'],
            $selectedApartment['user_id'],
            $apartmentAvgRating
        );
    }
}