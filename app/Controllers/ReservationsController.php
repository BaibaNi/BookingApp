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
            ->prepare('SELECT * from apartment_reservations where apartment_id = ? order by reserved_from asc ');
        $reservedApartmentsQuery->bindValue(1, $vars['id']);
        $reservationsInfo = $reservedApartmentsQuery
            ->executeQuery()
            ->fetchAllAssociative();

        $reservations = [];

        foreach ($reservationsInfo as $reservation) {

            $usersEmailsQuery = Database::connection()
                ->prepare('SELECT email FROM users where id = ?');
            $usersEmailsQuery->bindValue(1, $reservation['user_id']);
            $userEmail = $usersEmailsQuery
                ->executeQuery()
                ->fetchOne();


            $reservations[] = new ApartmentReservation(
                $reservation['reserved_from'],
                $reservation['reserved_until'],
                $userEmail,
                $reservation['id'],
                $reservation['user_id'],
                $reservation['apartment_id']
            );
        }

        return new View('Reservations/index', [
            'reservations' => $reservations
        ]);
    }


    public function reserve(array $vars): Redirect
    {

        $validator = new BookingFormValidator($_POST, [
            'date_range' => ['required'],
        ]);
        try{
            $validator->passes();

            $dateRange = explode('-', $_POST['date_range']);
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            $apartmentQuery = Database::connection()
                ->prepare('SELECT * FROM apartments where id = ?');
            $apartmentQuery->bindValue(1, $vars['id']);
            $apartmentInfo = $apartmentQuery
                ->executeQuery()
                ->fetchAssociative();


            $requiredFromCalendar = (Carbon::parse($startDate))->timestamp;
            $requiredUntilCalendar = (Carbon::parse($endDate))->timestamp;
            $availableFrom = (Carbon::parse($apartmentInfo['available_from']))->timestamp;
            $availableUntil = (Carbon::parse($apartmentInfo['available_until']))->timestamp;

            if($requiredFromCalendar > $availableFrom
                && $requiredFromCalendar < $availableUntil
                && $requiredUntilCalendar < $availableUntil
                && $requiredFromCalendar < $requiredUntilCalendar
            ){

                $reservedFrom = Carbon::createFromTimestamp($requiredFromCalendar)->format('Y-m-d');
                $reservedUntil = Carbon::createFromTimestamp($requiredUntilCalendar)->format('Y-m-d');

                $reservationsInPeriod = Database::connection()
                    ->prepare('SELECT * from apartment_reservations where apartment_id = ?
                                           and (reserved_from >= ? and reserved_until <= ?)');
                $reservationsInPeriod->bindValue(1, $vars['id']);
                $reservationsInPeriod->bindValue(2, $reservedFrom);
                $reservationsInPeriod->bindValue(3, $reservedUntil);
                $reservationsInfo = $reservationsInPeriod
                    ->executeQuery()
                    ->fetchAllAssociative();


                if(empty($reservationsInfo)){
                    Database::connection()
                        ->insert('apartment_reservations', [
                            'reserved_from' => $reservedFrom,
                            'reserved_until' => $reservedUntil,
                            'apartment_id' => $vars['id'],
                            'user_id' => $_SESSION['userid'],
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


//    public function cancel(array $vars): Redirect
//    {
//        var_dump($vars['id'],$vars['reservationid']);die;
//        Database::connection()
//            ->delete('apartment_reservations', [
//                'id' => (int)$vars['reservationid']
//            ]);
//
//        return new Redirect('/users/' . $vars['id']);
//    }
}