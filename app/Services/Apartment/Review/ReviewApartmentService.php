<?php
namespace App\Services\Apartment\Review;

use App\Repositories\Apartment\ApartmentRepository;
use App\Repositories\Apartment\DbalApartmentRepository;

class ReviewApartmentService
{

    private ApartmentRepository $apartmentRepository;

    public function __construct()
    {
        $this->apartmentRepository = new DbalApartmentRepository();
    }

    public function execute(ReviewApartmentRequest $request): array
    {
        $reviewParams = [
            'id' => $request->getApartmentId(),
            'userid' => $request->getUserId(),
            'review' => $request->getReview(),
            'rating' =>$request->getRating()
        ];

        $this->apartmentRepository->review($reviewParams);

        return $reviewParams;
    }
}