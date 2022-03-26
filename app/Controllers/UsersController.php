<?php
namespace App\Controllers;

use App\Database;
use App\Exceptions\LoginValidationException;
use App\Exceptions\RegistrationValidationException;
use App\Redirect;
use App\Services\User\Index\IndexUserService;
use App\Services\User\Login\LoginUserEmailRequest;
use App\Services\User\Login\LoginUserIdRequest;
use App\Services\User\Login\LoginUserService;
use App\Services\User\Register\RegisterUserRequest;
use App\Services\User\Register\RegisterUserService;
use App\Services\User\Show\ShowUserRequest;
use App\Services\User\Show\ShowUserService;
use App\Validation\Errors;
use App\Validation\LoginFormValidator;
use App\Validation\RegistrationFormValidator;
use App\View;


class UsersController extends Database
{

    public function main(): View
    {
        return new View('Main/index', [
            'errors' => Errors::getAll(),
            'inputs' => $_SESSION['inputs'] ?? []
        ]);
    }


    public function index(): View
    {

        $service = new IndexUserService();
        $users = $service->execute();

        return new View('Users/index', [
            'users' => $users
        ]);
    }


    public function show(array $vars): View
    {
        $userId = (int) $vars['id'];

        $service = new ShowUserService();
        $response = $service->execute(new ShowUserRequest($userId));

        return new View('Users/show', [
            'user' => $response->getUserProfile(),
            'createdApartments' => $response->getUserListedApartments(),
            'reservedApartments' => $response->getReservedApartments(),
            'reservations' => $response->getReservations()
        ]);
    }


    public function register(): Redirect
    {
        $validator = new RegistrationFormValidator($_POST, [
            'name' => ['required', 'Min:3'],
            'surname' => ['required', 'Min:3'],
            'birthday' => ['required'],
            'email_reg' => ['required', 'Min:3', 'Email:@', 'Email:.'],
            'password_reg' => ['required', 'Min:8'],
            'password_repeat' => ['required', 'Min:8']
        ]);
        try {
            $validator->passes();

            if($_POST['password_reg'] !== $_POST['password_repeat']){
                throw new RegistrationValidationException('Passwords do not match.');
            } else {

                $service = new RegisterUserService();
                $service->execute(new RegisterUserRequest(
                    $_POST['email_reg'],
                    $_POST['password_reg'],
                    $_POST['name'],
                    $_POST['surname'],
                    $_POST['birthday']
                ));

                $_SESSION['status_ok'] = 'Your account has been registered. Please, log-in for further actions!';
                return new Redirect('/');
            }
        } catch(RegistrationValidationException $exception){
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;
            return new Redirect("/");
        }

    }


    public function login(): Redirect
    {

        $userEmail = $_POST['email'];
        $userPassword = $_POST['password'];

        $validator = new LoginFormValidator($_POST, [
            'email' => ['required', 'Min:3', 'Email:@', 'Email:.'],
            'password' => ['required']
        ]);

        try{
            $validator->passes();

            $service = new LoginUserService();
            $user = $service->getUser(new LoginUserEmailRequest($userEmail));

            if(count($user) === 0){
                $_SESSION['status_err'] = 'User not found!';
                throw new LoginValidationException('User not found!');
            } else{
                $hashedPassword = $user['password'];
                if(password_verify($userPassword, $hashedPassword) ){

                    $userLogged = $service->execute(new LoginUserIdRequest($user['id']));

                    session_start();
                    $_SESSION['userid'] = $user['id'];
                    $_SESSION['username'] = $userLogged['name'];

                    return new Redirect('/');

                } else{
                    $_SESSION['status_err'] = 'Email or password is not correct!';
                    throw new LoginValidationException('Email or password is not correct!');
                }
            }
        } catch(LoginValidationException $exception){
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;
            return new Redirect('/');
        }

    }


    public function logout(): Redirect
    {
        $result = null;
        if(isset($_SESSION['userid'])){

            unset($_SESSION['username']);
            unset($_SESSION['userid']);
            session_destroy();

            $result = new Redirect('/');
        }

        return $result;
    }
}