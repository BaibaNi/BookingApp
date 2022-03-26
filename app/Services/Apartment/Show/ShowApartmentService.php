<?php
namespace App\Services\Apartment\Show;

use App\Models\Apartment;
use App\Models\ApartmentReview;
use App\Repositories\Apartment\ApartmentRepository;
use App\Repositories\Apartment\DbalApartmentRepository;
use Carbon\CarbonPeriod;

class ShowApartmentService
{

    private ApartmentRepository $apartmentRepository;

    public function __construct()
    {
        $this->apartmentRepository = new DbalApartmentRepository();
    }


    public function execute(ShowApartmentRequest $request): ShowApartmentResponse
    {
        $apartmentId = $request->getApartmentId();

        $apartmentInfo = $this->apartmentRepository->getApartmentById($apartmentId);
        $apartmentAvgRating = $this->apartmentRepository->getApartmentAvgRatingById($apartmentId);

        $apartment = new Apartment(
            $apartmentInfo['title'],
            $apartmentInfo['address'],
            $apartmentInfo['description'],
            $apartmentInfo['price'],
            date('m-d-Y', strtotime($apartmentInfo['available_from'])),
            date('m-d-Y', strtotime($apartmentInfo['available_until'])),
            $apartmentInfo['id'],
            $apartmentInfo['user_id'],
            $apartmentAvgRating
        );


        $reviewsList = $this->apartmentRepository->getReviewsById($apartmentId);

        $reviews = [];
        foreach($reviewsList as $review){
            $reviews[] = new ApartmentReview(
                $review['name'],
                $review['surname'],
                $review['review'],
                $review['rating'],
                $review['created_at'],
                $review['id'],
                $review['apartment_id']
            );
        }


        $reservationsInfo = $this->apartmentRepository->getReservationsById($apartmentId);

        $reservations = [];
        foreach ($reservationsInfo as $reservation){
            $reservations[] = [$reservation['reserved_from'], $reservation['reserved_until']];
        }

        $reservationDates = [];
        foreach ($reservations as $record){
            [$startDate, $endDate] = $record;
            $period = CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date){
                $reservationDates[] = $date->format('m/d/Y');
            }
        }

        $totalPrice = count($reservationDates) * $apartmentInfo['price'];

        return new ShowApartmentResponse(
            $apartment,
            $reviews,
            $reservationDates,
            $totalPrice
        );
    }
}