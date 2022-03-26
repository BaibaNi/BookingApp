<?php

namespace App\Repositories\Apartment;

use App\Models\Apartment;

interface ApartmentRepository
{
    /**
     * INDEX APARTMENTS
    */
    public function getApartmentList(): array;
    public function getApartmentAvgRatingById(int $apartmentId): float;

    /**
     * SHOW APARTMENT
     */
    public function getApartmentById(int $apartmentId): array;
    public function getReviewsById(int $apartmentId): array;
    public function getReservationsById(int $apartmentId): array;

    /**
     * CREATE APARTMENT
    */
    public function create(Apartment $apartment): void;

    /**
     * EDIT APARTMENT
     */
    public function edit(array $apartmentParams): void;

    /**
     * DELETE APARTMENT
    */
    public function delete(int $apartmentId): void;

    /**
     * LEAVE REVIEW
     */
    public function review(array $reviewParams): void;
}