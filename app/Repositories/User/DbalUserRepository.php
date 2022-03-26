<?php
namespace App\Repositories\User;

use App\Database;
use App\Models\User;
use App\Models\UserProfile;

class DbalUserRepository implements UserRepository
{
    /**
     * USER LIST
    */
    public function getUsers(): array
    {
        return Database::connection()
            ->prepare('SELECT * FROM users')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * SHOW USER
    */
    public function getUserById(int $userId): array
    {
        $usersQuery = Database::connection()
            ->prepare('SELECT * FROM users where id = ?');
        $usersQuery->bindValue(1, $userId);

        return $usersQuery
            ->executeQuery()
            ->fetchAssociative();
    }

    public function getUserProfileById(int $userId): array
    {
        $stmt = Database::connection()
            ->prepare('SELECT * FROM user_profiles WHERE user_id = ?');
        $stmt->bindValue(1, $userId);

        return $stmt
            ->executeQuery()
            ->fetchAssociative();
    }

    public function getListedApartmentsById(int $userId): array{
        $createdApartmentQuery = Database::connection()
            ->prepare('SELECT * FROM apartments where user_id = ? order by available_from desc ');
        $createdApartmentQuery->bindValue(1, $userId);

        return $createdApartmentQuery
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

    public function getReservedApartmentsById(int $userId): array
    {
        $reservedApartmentsQuery = Database::connection()
            ->prepare('SELECT * from apartment_reservations
                            join apartments on (apartment_reservations.apartment_id = apartments.id) 
                            and apartment_reservations.user_id = ? order by reserved_from asc ');
        $reservedApartmentsQuery->bindValue(1, $userId);

        return $reservedApartmentsQuery
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function getReservationsById(int $userId): array
    {
        $reservationsQuery = Database::connection()
            ->prepare('SELECT * from apartment_reservations 
                            join users on (apartment_reservations.user_id = users.id) 
                            and apartment_reservations.user_id = ?');
        $reservationsQuery->bindValue(1, $userId);

        return $reservationsQuery
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * USER REGISTRATION
    */
    public function storeUser(User $user): void
    {
        Database::connection()
            ->insert('users', [
                'email' => $user->getEmail(),
                'password' => password_hash($user->getPassword(), PASSWORD_BCRYPT),
            ]);
    }

    public function getLastInsertedUser(): array
    {
        return Database::connection()
            ->prepare('SELECT * FROM users WHERE id = LAST_INSERT_ID()')
            ->executeQuery()
            ->fetchAssociative();
    }

    public function registerUserProfile(UserProfile $userProfile): void
    {
        Database::connection()
            ->insert('user_profiles', [
                'user_id' => $userProfile->getId(),
                'name' => $userProfile->getName(),
                'surname' => $userProfile->getSurname(),
                'birthday' => $userProfile->getBirthday(),
            ]);
    }


    /**
     * USER LOGIN
     */
    public function getUserByEmail(string $userEmail): array
    {
        $stmt = Database::connection()
            ->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->bindValue(1, $userEmail);

        return $stmt
            ->executeQuery()
            ->fetchAssociative();
    }



}
