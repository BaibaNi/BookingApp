<?php
namespace App\Controllers;

use App\Database;
use App\Exceptions\BookingValidationException;
use App\Models\ApartmentReservation;
use App\Models\User;
use App\Redirect;
use App\Validation\BookingFormValidator;
use App\View;
use Carbon\Carbon;

class ReservationsController extends Database
{
    public function index(array $vars): View
    {
        $reservedApartmentsQuery = Database::connection()
            ->prepare('SELECT * from apartment_reservations where apartment_id = ?');
        $reservedApartmentsQuery->bindValue(1, $vars['id']);
        $reservationsInfo = $reservedApartmentsQuery
            ->executeQuery()
            ->fetchAllAssociative();

        $reservations = [];
        $users = [];

        foreach ($reservationsInfo as $reservation) {

            $usersListQuery = Database::connection()
                ->prepare('SELECT * FROM users where id = ?');
            $usersListQuery->bindValue(1, $reservation['user_id']);
            $usersList = $usersListQuery
                ->executeQuery()
                ->fetchAllAssociative();


            $reservations[] = new ApartmentReservation(
                $reservation['reserved_from'],
                $reservation['reserved_until'],
                $reservation['id'],
                $reservation['user_id'],
                $reservation['apartment_id']
            );

            foreach ($usersList as $user) {
                $users[] = new User(
                    $user['email'],
                    $user['password'],
                    $user['created_at'],
                    $user['id']
                );
            }
        }

        return new View('Reservations/index', [
            'users' => $users,
            'reservations' => $reservations
        ]);
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


    public function cancel(array $vars): Redirect
    {
        var_dump($vars['id'],$vars['reservationid']);die;
        Database::connection()
            ->delete('apartment_reservations', [
                'id' => (int)$vars['reservationid']
            ]);

        return new Redirect('/users/' . $vars['id']);
    }
}