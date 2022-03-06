<?php
namespace App\Controllers;

use App\Database;
use App\Exceptions\ApartmentValidationException;
use App\Exceptions\BookingValidationException;
use App\Exceptions\EditValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ReviewValidationException;
use App\Models\Apartment;
use App\Models\ApartmentAverageRating;
use App\Models\ApartmentReview;
use App\Redirect;
use App\Validation\ApartmentFormValidator;
use App\Validation\BookingFormValidator;
use App\Validation\EditFormValidator;
use App\Validation\Errors;
use App\Validation\ReviewFormValidator;
use App\View;
use Carbon\Carbon;

class ApartmentsController extends Database
{
    public function index(): View
    {
        $apartmentList = Database::connection()
            ->prepare('SELECT * FROM apartments order by available_from asc')
            ->executeQuery()
            ->fetchAllAssociative();

        $apartments = [];
        foreach ($apartmentList as $apartment){
            $apartments[] = new Apartment(
                $apartment['title'],
                $apartment['address'],
                $apartment['description'],
                $apartment['available_from'],
                $apartment['available_until'],
                $apartment['id'],
                $apartment['user_id']
            );
        }


        $ratingsList = Database::connection()
            ->prepare('SELECT distinct apartment_id from apartment_reviews')
            ->executeQuery()
            ->fetchAllAssociative();

        $avgRatings = [];
        foreach ($ratingsList as $apartment) {

            $apartmentAvgRatingQuery = Database::connection()
                ->prepare('SELECT AVG(rating) from apartment_reviews where apartment_id = ?');
            $apartmentAvgRatingQuery->bindValue(1, $apartment['apartment_id']);
            $apartmentAvgRating = $apartmentAvgRatingQuery
                ->executeQuery()
                ->fetchAssociative();


            $avgRatings[] = new ApartmentAverageRating(
                $apartment['apartment_id'],
                (float) number_format($apartmentAvgRating['AVG(rating)'], 2)
            );

        }

        return new View('Apartments/index', [
            'apartments' => $apartments,
            'avgRatings' => $avgRatings
            ]);
    }


    public function show(array $vars): View
    {

        $apartmentQuery = Database::connection()
            ->prepare('SELECT * FROM apartments where id = ?');
        $apartmentQuery->bindValue(1, $vars['id']);
        $apartmentInfo = $apartmentQuery
            ->executeQuery()
            ->fetchAssociative();

        $apartment = new Apartment(
            $apartmentInfo['title'],
            $apartmentInfo['address'],
            $apartmentInfo['description'],
            $apartmentInfo['available_from'],
            $apartmentInfo['available_until'],
            $apartmentInfo['id'],
            $apartmentInfo['user_id']
        );



        $reviewsQuery = Database::connection()
            ->prepare('SELECT * from apartment_reviews join user_profiles 
    on (apartment_reviews.user_id = user_profiles.user_id) and apartment_reviews.apartment_id = ? order by created_at desc');
        $reviewsQuery->bindValue(1, (int) $vars['id']);
        $reviewsList = $reviewsQuery->executeQuery()->fetchAllAssociative();

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


        $ratingQuery = Database::connection()
            ->prepare('SELECT AVG(rating) from apartment_reviews where apartment_id = ?');
        $ratingQuery->bindValue(1, (int) $vars['id']);
        $avgRating = $ratingQuery->executeQuery()->fetchAssociative();

        return new View('Apartments/show', [
            'apartment' => $apartment,
            'reviews' => $reviews,
            'avgRating' => number_format($avgRating['AVG(rating)'], 2),
            'errors' => Errors::getAll(),
            'inputs' => $_SESSION['inputs'] ?? []
        ]);
    }


    public function createForm(): View
    {
        return new View('Apartments/create', [
            'errors' => Errors::getAll(),
            'inputs' => $_SESSION['inputs'] ?? []
        ]);
    }


    public function create(): Redirect
    {
        $validator = new ApartmentFormValidator($_POST, [
            'title' => ['required', 'min:3'],
            'address' => ['required'],
            'description' => ['required'],
        ]);
        try{
            $validator->passes();

            if(empty($_POST['available_from'])){
                $availableFrom = Carbon::now();
            } else{
                $availableFrom = $_POST['available_from'];
            }

            if(empty($_POST['available_until'])){
                $availableUntil = Carbon::create(2032, 01, 01, 0, 0, 0, 'GMT');
            } else{
                $availableUntil = $_POST['available_until'];
            }


            Database::connection()
                ->insert('apartments', [
                    'user_id' => $_SESSION['userid'],
                    'title' => $_POST['title'],
                    'address' => $_POST['address'],
                    'description' => $_POST['description'],
                    'available_from' => $availableFrom,
                    'available_until' => $availableUntil
                ]);

            return new Redirect('/users/' . $_SESSION['userid']);

        } catch(ApartmentValidationException $exception){
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;
            return new Redirect('/apartments/create');
        }
    }


    public function delete(array $vars): Redirect
    {
        Database::connection()
            ->delete('apartments', [
                'id' => (int)$vars['id'],
            ]);

        return new Redirect('/apartments');
    }


    public function editForm(array $vars): View
    {
        try {
            $stmt = Database::connection()
                ->prepare('SELECT * FROM apartments where id = ?');
            $stmt->bindValue(1, $vars['id']);
            $list = $stmt
                ->executeQuery()
                ->fetchAssociative();

            if (!$list) {
                throw new ResourceNotFoundException("Apartment with id {$vars['id']} is not found.");
            }

            $apartment = new Apartment(
                $list['title'],
                $list['address'],
                $list['description'],
                $list['available_from'],
                $list['available_until'],
                $list['id'],
                $list['user_id']
            );

            return new View('Apartments/edit', [
                'apartment' => $apartment,
                'errors' => Errors::getAll(),
                'inputs' => $_SESSION['inputs'] ?? []
            ]);
        } catch (ResourceNotFoundException $exception){
            var_dump($exception->getMessage());
            return new View('404');
        }
    }


    public function edit(array $vars): Redirect
    {

        $validator = new EditFormValidator($_POST, [
            'title' => ['required', 'min:3'],
            'address' => ['required'],
            'description' => ['required'],
            'available_from' => ['required'],
            'available_until' => ['required'],
        ]);

        try{
            $validator->passes();

            Database::connection()
                ->update('apartments', [
                    'title' => $_POST['title'],
                    'address' => $_POST['address'],
                    'description' => $_POST['description'],
                ], [
                        'id' => (int)$vars['id'],
                    ]
                );

            return new Redirect('/apartments/' . (int) $vars['id']);
        } catch (EditValidationException $exception){
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;
            return new Redirect('/apartments/' . (int) $vars['id'] . '/edit');
        }

    }


    public function reserve(array $vars): Redirect
    {
        $validator = new BookingFormValidator($_POST, [
            'available_from' => ['required'],
            'available_until' => ['required']
        ]);
        try{
            $validator->passes();

            $apartmentQuery = Database::connection()
                ->prepare('SELECT * FROM apartments where id = ?');
            $apartmentQuery->bindValue(1, $vars['id']);
            $apartmentInfo = $apartmentQuery
                ->executeQuery()
                ->fetchAssociative();


            $requiredFrom = (Carbon::parse($_POST['available_from']))->timestamp;
            $requiredUntil = (Carbon::parse($_POST['available_until']))->timestamp;
            $availableFrom = (Carbon::parse($apartmentInfo['available_from']))->timestamp;
            $availableUntil = (Carbon::parse($apartmentInfo['available_until']))->timestamp;

            if($requiredFrom > $availableFrom
                && $requiredFrom < $availableUntil
                && $requiredUntil < $availableUntil
            ){

                $reservationsInPeriod = Database::connection()
                    ->prepare('SELECT * from apartment_reservations where apartment_id = ?
                                           and (reserved_from >= ? and reserved_until <= ?)');
                $reservationsInPeriod->bindValue(1, $vars['id']);
                $reservationsInPeriod->bindValue(2, $_POST['available_from']);
                $reservationsInPeriod->bindValue(3, $_POST['available_until']);
                $reservationsInfo = $reservationsInPeriod
                    ->executeQuery()
                    ->fetchAllAssociative();

//                var_dump($reservationsInfo); die;

                if(empty($reservationsInfo)){
                    Database::connection()
                        ->insert('apartment_reservations', [
                            'reserved_from' => $_POST['available_from'],
                            'reserved_until' => $_POST['available_until'],
                            'apartment_id' => $vars['id'],
                            'user_id' => $_SESSION['userid'],
                        ]);

                        Database::connection()
                            ->update('apartments', [
                                'available_from' => $_POST['available_until'],
                            ], [
                                'id' => $vars['id']
                            ]);

                } else{
                    throw new BookingValidationException('Selected period is not available');
                }

            } else {
                throw new BookingValidationException('Selected period is not available');
            }

            return new Redirect('/users/' . $_SESSION['userid']);

        } catch(BookingValidationException $exception){
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;
            return new Redirect('/apartments/' . $vars['id']);
        }

    }


    public function review(array $vars): Redirect
    {
        $validator = new ReviewFormValidator($_POST, [
            'review' => ['required', 'Min:3'],
            'rating' => ['required']
        ]);
        try {
            $validator->passes();


            Database::connection()
                ->insert('apartment_reviews', [
                    'apartment_id' => $vars['id'],
                    'user_id' => $_SESSION['userid'],
                    'review' => $_POST['review'],
                    'rating' => $_POST['rating']
                ]);

            return new Redirect("/apartments/{$vars['id']}");
        } catch(ReviewValidationException $exception){
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;
            return new Redirect("/apartments/{$vars['id']}");
        }
    }


}