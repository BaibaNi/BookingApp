<?php
namespace App\Controllers;

use App\Database;
use App\Exceptions\LoginValidationException;
use App\Exceptions\RegistrationValidationException;
use App\Models\Apartment;
use App\Models\ApartmentReservation;
use App\Models\User;
use App\Models\UserProfile;
use App\Redirect;
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
        $usersList = Database::connection()
            ->prepare('SELECT * FROM users')
            ->executeQuery()
            ->fetchAllAssociative();

        $users = [];
        foreach ($usersList as $user){
            $users[] = new User(
                $user['email'],
                $user['password'],
                $user['created_at'],
                $user['id']
            );
        }

        return new View('Users/index', [
            'users' => $users
        ]);
    }


    public function show(array $vars): View
    {
//---User's profile
        $usersQuery = Database::connection()
            ->prepare('SELECT * FROM users where id = ?');
        $usersQuery->bindValue(1, $vars['id']);
        $userList = $usersQuery
            ->executeQuery()
            ->fetchAssociative();

        $userProfileQuery = Database::connection()
            ->prepare('SELECT * FROM user_profiles where user_id = ?');
        $userProfileQuery->bindValue(1, $vars['id']);
        $userProfile = $userProfileQuery
            ->executeQuery()
            ->fetchAssociative();


        $user = new UserProfile(
            $userProfile['name'],
            $userProfile['surname'],
            $userProfile['birthday'],
            $userList['email'],
            $userList['password'],
            $userList['created_at'],
            $userList['id']
        );

//---created apartments for booking
        $createdApartmentQuery = Database::connection()
            ->prepare('SELECT * FROM apartments where user_id = ? order by available_from desc ');
        $createdApartmentQuery->bindValue(1, $_SESSION['userid']);
        $apartmentInfo = $createdApartmentQuery
            ->executeQuery()
            ->fetchAllAssociative();

        $createdApartments = [];
        foreach ($apartmentInfo as $apartment){

            $apartmentAvgRatingQuery = Database::connection()
                ->prepare('SELECT AVG(rating) from apartment_reviews where apartment_id = ?');
            $apartmentAvgRatingQuery->bindValue(1, $apartment['id']);
            $apartmentAvgRating = $apartmentAvgRatingQuery
                ->executeQuery()
                ->fetchAssociative();

            $createdApartments[] = new Apartment(
                 $apartment['title'],
                 $apartment['address'],
                 $apartment['description'],
                 $apartment['price'],
                 $apartment['available_from'],
                 $apartment['available_until'],
                 $apartment['id'],
                 $apartment['user_id'],
                (float) number_format($apartmentAvgRating['AVG(rating)'], 2)
            );
        }

//---reserved apartments
        $reservedApartmentsQuery = Database::connection()
            ->prepare('SELECT * from apartment_reservations
    join apartments on (apartment_reservations.apartment_id = apartments.id) and apartment_reservations.user_id = ? 
    order by reserved_from asc ');
        $reservedApartmentsQuery->bindValue(1, $_SESSION['userid']);
        $reservedApartmentsInfo = $reservedApartmentsQuery
            ->executeQuery()
            ->fetchAllAssociative();

        $reservedApartments = [];
        foreach ($reservedApartmentsInfo as $reservation){

            $apartmentAvgRatingQuery = Database::connection()
                ->prepare('SELECT AVG(rating) from apartment_reviews where apartment_id = ?');
            $apartmentAvgRatingQuery->bindValue(1, $reservation['id']);
            $apartmentAvgRating = $apartmentAvgRatingQuery
                ->executeQuery()
                ->fetchAssociative();

            $reservedApartments[] = new Apartment(
                $reservation['title'],
                $reservation['address'],
                $reservation['description'],
                $reservation['price'],
                $reservation['reserved_from'],
                $reservation['reserved_until'],
                $reservation['id'],
                $reservation['user_id'],
                (float) number_format($apartmentAvgRating['AVG(rating)'], 2)
            );
        }


        $reservationsQuery = Database::connection()
            ->prepare('SELECT * from apartment_reservations 
    join users on (apartment_reservations.user_id = users.id) and apartment_reservations.user_id = ?');
        $reservationsQuery->bindValue(1, $vars['id']);
        $reservationsInfo = $reservationsQuery
            ->executeQuery()
            ->fetchAllAssociative();

        $reservations =[];
        foreach ($reservationsInfo as $reservation) {

            $reservations[] = new ApartmentReservation(
                $reservation['reserved_from'],
                $reservation['reserved_until'],
                $reservation['email'],
                $reservation['id'],
                $reservation['user_id'],
                $reservation['apartment_id']
            );
        }


        return new View('Users/show', [
            'user' => $user,
            'createdApartments' => $createdApartments,
            'reservedApartments' => $reservedApartments,
            'reservations' => $reservations
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

                Database::connection()
                    ->insert('users', [
                        'email' => $_POST['email_reg'],
                        'password' => password_hash($_POST['password_reg'], PASSWORD_BCRYPT),
                    ]);

                $res = Database::connection()
                    ->prepare('SELECT * FROM users WHERE id = LAST_INSERT_ID()')
                    ->executeQuery()
                    ->fetchAssociative();


                Database::connection()
                    ->insert('user_profiles', [
                        'user_id' => (int)$res['id'],
                        'name' => $_POST['name'],
                        'surname' => $_POST['surname'],
                        'birthday' => $_POST['birthday'],
                    ]);

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

            $stmt = Database::connection()
                ->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->bindValue(1, $userEmail);
            $user = $stmt
                ->executeQuery()
                ->fetchAssociative();

            if(count($user) === 0){
                $_SESSION['status_err'] = 'User not found!';
                throw new LoginValidationException('User not found!');
            } else{
                $hashedPassword = $user['password'];
                if(password_verify($userPassword, $hashedPassword) ){

                    $stmt = Database::connection()
                        ->prepare('SELECT * FROM user_profiles WHERE user_id = ?');
                    $stmt->bindValue(1, $user['id']);
                    $userLogged = $stmt
                        ->executeQuery()
                        ->fetchAssociative();

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