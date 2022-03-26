<?php
namespace App\Controllers;

use App\Database;
use App\Exceptions\ApartmentValidationException;
use App\Exceptions\EditValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ReviewValidationException;
use App\Redirect;
use App\Services\Apartment\Create\CreateApartmentRequest;
use App\Services\Apartment\Create\CreateApartmentService;
use App\Services\Apartment\Delete\DeleteApartmentRequest;
use App\Services\Apartment\Delete\DeleteApartmentService;
use App\Services\Apartment\Edit\EditApartmentRequest;
use App\Services\Apartment\Edit\EditApartmentService;
use App\Services\Apartment\EditForm\EditFormApartmentRequest;
use App\Services\Apartment\EditForm\EditFormApartmentService;
use App\Services\Apartment\Index\IndexApartmentService;
use App\Services\Apartment\Review\ReviewApartmentRequest;
use App\Services\Apartment\Review\ReviewApartmentService;
use App\Services\Apartment\Show\ShowApartmentRequest;
use App\Services\Apartment\Show\ShowApartmentService;
use App\Validation\ApartmentFormValidator;
use App\Validation\EditFormValidator;
use App\Validation\Errors;
use App\Validation\ReviewFormValidator;
use App\View;
use Carbon\Carbon;


class ApartmentsController extends Database
{
    public function index(): View
    {
        $service = new IndexApartmentService();
        $response = $service->execute();

        return new View('Apartments/index', [
            'apartments' => $response->getApartments(),
            ]);
    }


    public function show(array $vars): View
    {
        $apartmentId = (int) $vars['id'];

        $service = new ShowApartmentService();
        $response = $service->execute(new ShowApartmentRequest($apartmentId));

        return new View('Apartments/show', [
            'apartment' => $response->getApartment(),
            'reviews' => $response->getReviews(),
            'reservationDates' => $response->getReservationDates(),
            'totalPrice' => $response->getTotalPrice(),
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
            'price' => ['required']
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

            $service = new CreateApartmentService();
            $service->execute(new CreateApartmentRequest(
                $_SESSION['userid'],
                $_POST['title'],
                $_POST['address'],
                $_POST['description'],
                $_POST['price'],
                $availableFrom,
                $availableUntil
                )
            );

            $_SESSION['status_ok'] = 'Your apartment is listed!';
            return new Redirect('/users/' . $_SESSION['userid']);

        } catch(ApartmentValidationException $exception){
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;
            return new Redirect('/apartments/create');
        }
    }


    public function delete(array $vars): Redirect
    {

        $apartmentID = (int) $vars['id'];
        try{

            $service = new DeleteApartmentService();
            $reservationsInfo = $service->check(new DeleteApartmentRequest($apartmentID));

            if (!empty($reservationsInfo)) {
                $_SESSION['status_err'] = 'Cannot delete! Your listed apartment has reservations.';
                throw new ResourceNotFoundException("Apartment has reservations.");
            } else{

                $service->execute(new DeleteApartmentRequest($apartmentID));

                $_SESSION['status_ok'] = 'Your listed apartment is deleted.';
            }
            return new Redirect('/users/' . $_SESSION['userid']);

        } catch(ResourceNotFoundException $exception){
            $_SESSION['status_err'] = 'Cannot delete! Your listed apartment has active reservations.';
            return new Redirect('/users/' . $_SESSION['userid']);
        }

    }


    public function editForm(array $vars): View
    {

        $apartmentId = (int) $vars['id'];

        $service = new EditFormApartmentService();
        $apartment = $service->execute(new EditFormApartmentRequest($apartmentId));

        return new View('Apartments/edit', [
            'apartment' => $apartment,
            'errors' => Errors::getAll(),
            'inputs' => $_SESSION['inputs'] ?? []
        ]);
    }


    public function edit(array $vars): Redirect
    {
        $apartmentID = (int) $vars['id'];

        $validator = new EditFormValidator($_POST, [
            'title' => ['required', 'min:3'],
            'address' => ['required'],
            'description' => ['required']
        ]);

        try{
            $validator->passes();

            $service = new EditApartmentService();
            $service->execute(new EditApartmentRequest(
                $apartmentID,
                $_POST['title'],
                $_POST['address'],
                $_POST['description'],
                $_POST['price'],
            ));

            $_SESSION['status_ok'] = 'Changes saved.';
            return new Redirect('/apartments/' . (int) $vars['id']);
        } catch (EditValidationException $exception){
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;
            return new Redirect('/apartments/' . (int) $vars['id'] . '/edit');
        }

    }


    public function review(array $vars): Redirect
    {

        $apartmentId = (int) $vars['id'];

        $validator = new ReviewFormValidator($_POST, [
            'review' => ['required', 'Min:3'],
            'rating' => ['required']
        ]);
        try {
            $validator->passes();

            $service = new ReviewApartmentService();
            $service->execute(new ReviewApartmentRequest(
                $apartmentId,
                $_SESSION['userid'],
                $_POST['review'],
                $_POST['rating']
            ));

            $_SESSION['status_ok'] = 'Thank you! Your feedback is registered!';
            return new Redirect("/apartments/{$vars['id']}");

        } catch(ReviewValidationException $exception){
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;
            return new Redirect("/apartments/{$vars['id']}");
        }
    }
}