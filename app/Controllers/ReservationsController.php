<?php
namespace App\Controllers;

use App\Database;
use App\Exceptions\BookingValidationException;
use App\Redirect;
use App\Services\Reservation\Index\IndexReservationRequest;
use App\Services\Reservation\Index\IndexReservationService;
use App\Services\Reservation\Reserve\ReserveReservationApartmentsRequest;
use App\Services\Reservation\Reserve\ReserveReservationPeriodRequest;
use App\Services\Reservation\Reserve\ReserveReservationRequest;
use App\Services\Reservation\Reserve\ReserveReservationService;
use App\Validation\BookingFormValidator;
use App\View;
use Carbon\Carbon;

class ReservationsController extends Database
{

    public function index(array $vars): View
    {
        $apartmentId = (int) $vars['id'];

        $service = new IndexReservationService();
        $reservations = $service->execute(new IndexReservationRequest($apartmentId));

        return new View('Reservations/index', [
            'reservations' => $reservations
        ]);
    }


    public function reserve(array $vars): Redirect
    {

        $apartmentId = (int) $vars['id'];
        $validator = new BookingFormValidator($_POST, [
            'date_range' => ['required'],
        ]);
        try{
            $validator->passes();

            $dateRange = explode('-', $_POST['date_range']);
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];


            $service = new ReserveReservationService();
            $apartmentInfo = $service->checkApartment(new ReserveReservationApartmentsRequest($apartmentId));


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

                $service = new ReserveReservationService();
                $reservationsInfo = $service->checkReservations(new ReserveReservationPeriodRequest(
                    $apartmentId,
                    $reservedFrom,
                    $reservedUntil
                ));

                if(!empty($reservationsInfo)){
                    $_SESSION['status_err'] = 'Selected period is not available!';
                    throw new BookingValidationException('Selected period is not available');

                } else{

                    $service = new ReserveReservationService();
                    $service->execute(new ReserveReservationRequest(
                        $apartmentId,
                        $reservedFrom,
                        $reservedUntil,
                        $_SESSION['userid']
                    ));

                    $_SESSION['status_ok'] = 'Registration is successful!';
                    return new Redirect('/apartments/' . $apartmentId);
                }

            } else {
                $_SESSION['status_err'] = 'Selected period is not available!';
                throw new BookingValidationException('Selected period is not available');
            }

        } catch(BookingValidationException $exception){
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;
            return new Redirect('/apartments/' . $apartmentId);
        }

    }

}