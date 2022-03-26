<?php

namespace App\Repositories\Apartment;


use App\Database;
use App\Models\Apartment;

class DbalApartmentRepository implements ApartmentRepository
{
    /**
     * INDEX APARTMENTS
     */

    public function getApartmentList(): array
    {
        return Database::connection()
            ->prepare('SELECT * FROM apartments order by available_from asc')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function getApartmentAvgRatingById(int $apartmentId): float
    {
        $apartmentAvgRatingQuery = Database::connection()
            ->prepare('SELECT AVG(rating) from apartment_reviews where apartment_id = ?');
        $apartmentAvgRatingQuery->bindValue(1, $apartmentId);
        $apartmentAvgRating = $apartmentAvgRatingQuery
            ->executeQuery()
            ->fetchAssociative();

        return (float) number_format($apartmentAvgRating['AVG(rating)'], 2);
    }


    /**
     * SHOW APARTMENT
    */
    public function getApartmentById(int $apartmentId): array
    {
        $apartmentQuery = Database::connection()
            ->prepare('SELECT * FROM apartments where id = ?');
        $apartmentQuery->bindValue(1, $apartmentId);

        return $apartmentQuery
            ->executeQuery()
            ->fetchAssociative();
    }

    public function getReviewsById(int $apartmentId): array
    {
        $reviewsQuery = Database::connection()
            ->prepare('SELECT * from apartment_reviews join user_profiles 
                            on (apartment_reviews.user_id = user_profiles.user_id) 
                            and apartment_reviews.apartment_id = ? order by created_at desc');
        $reviewsQuery->bindValue(1, $apartmentId);

        return $reviewsQuery
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function getReservationsById(int $apartmentId): array
    {
        $reservedApartmentsQuery = Database::connection()
            ->prepare('SELECT * from apartment_reservations
                            join apartments on (apartment_reservations.apartment_id = apartments.id) 
                            and apartment_reservations.apartment_id = ?');
        $reservedApartmentsQuery->bindValue(1, $apartmentId);

        return $reservedApartmentsQuery
            ->executeQuery()
            ->fetchAllAssociative();
    }


    /**
     * CREATE APARTMENT
    */
    public function create(Apartment $apartment): void
    {
        Database::connection()
        ->insert('apartments', [
            'user_id' => $apartment->getUserId(),
            'title' => $apartment->getTitle(),
            'address' => $apartment->getAddress(),
            'description' => $apartment->getDescription(),
            'price' => $apartment->getPrice(),
            'available_from' => $apartment->getAvailableFrom(),
            'available_until' => $apartment->getAvailableUntil()
        ]);
    }

    /**
     * EDIT APARTMENT
    */
    public function edit(array $apartmentParams): void
    {
        Database::connection()
            ->update('apartments', [
                'title' => $apartmentParams['title'],
                'address' => $apartmentParams['address'],
                'description' => $apartmentParams['description'],
                'price' => $apartmentParams['price']
            ], [
                    'id' => $apartmentParams['id'],
                ]
            );
    }

    /**
     * DELETE APARTMENT
    */
    public function delete(int $apartmentId): void
    {
        Database::connection()
            ->delete('apartments', [
                'id' => $apartmentId,
            ]);
    }


    /**
     * LEAVE REVIEW
    */
    public function review(array $reviewParams): void
    {
        Database::connection()
            ->insert('apartment_reviews', [
                'apartment_id' => $reviewParams['id'],
                'user_id' => $reviewParams['userid'],
                'review' => $reviewParams['review'],
                'rating' => $reviewParams['rating']
            ]);
    }

}