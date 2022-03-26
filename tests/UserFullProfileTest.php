<?php
declare(strict_types=1);

namespace Tests;

use App\Models\UserProfile;
use PHPUnit\Framework\TestCase;

class UserFullProfileTest extends TestCase
{
    public function testGetName(): void
    {
        $reservation = new UserProfile('TestName', 'TestSurname', '1999-03-21', 'test@email.com', 'password', '2022-01-02', 3);
        $this->assertSame('TestName', $reservation->getName());
    }
    public function testGetSurname(): void
    {
        $reservation = new UserProfile('TestName', 'TestSurname', '1999-03-21', 'test@email.com', 'password', '2022-01-02', 3);
        $this->assertSame('TestSurname', $reservation->getSurname());
    }

    public function testGetBirthday(): void
    {
        $reservation = new UserProfile('TestName', 'TestSurname', '1999-03-21', 'test@email.com', 'password', '2022-01-02', 3);
        $this->assertSame('1999-03-21', $reservation->getBirthday());
    }

    public function testGetEmail(): void
    {
        $reservation = new UserProfile('TestName', 'TestSurname', '1999-03-21', 'test@email.com', 'password', '2022-01-02', 3);
        $this->assertSame('test@email.com', $reservation->getEmail());
    }

    public function testGetPassword(): void
    {
        $reservation = new UserProfile('TestName', 'TestSurname', '1999-03-21', 'test@email.com', 'password', '2022-01-02', 3);
        $this->assertSame('password', $reservation->getPassword());
    }

    public function testGetCreatedAt(): void
    {
        $reservation = new UserProfile('TestName', 'TestSurname', '1999-03-21', 'test@email.com', 'password', '2022-01-02', 3);
        $this->assertSame('2022-01-02', $reservation->getCreatedAt());
    }

    public function testGetId(): void
    {
        $reservation = new UserProfile('TestName', 'TestSurname', '1999-03-21', 'test@email.com', 'password', '2022-01-02', 3);
        $this->assertSame(3, $reservation->getId());
    }
}
