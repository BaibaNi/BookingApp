<?php
namespace App\Services\Apartment\Index;

use App\Database;
use App\Models\Apartment;
use App\Repositories\Apartment\ApartmentRepository;
use App\Repositories\Apartment\DbalApartmentRepository;

class IndexApartmentService
{
    private ApartmentRepository $apartmentRepository;

    public function __construct()
    {
        $this->apartmentRepository = new DbalApartmentRepository();
    }

    public function execute(): IndexApartmentResponse
    {

        $apartmentList = $this->apartmentRepository->getApartmentList();


        $apartments = [];
        foreach ($apartmentList as $apartment){

            $apartmentAvgRating = $this->apartmentRepository->getApartmentAvgRatingById($apartment['id']);

            $apartments[] = new Apartment(
                $apartment['title'],
                $apartment['address'],
                $apartment['description'],
                $apartment['price'],
                $apartment['available_from'],
                $apartment['available_until'],
                $apartment['id'],
                $apartment['user_id'],
                $apartmentAvgRating
            );
        }


        return new IndexApartmentResponse($apartments);
    }
}
