<?php
declare(strict_types=1);

namespace Tests;

use App\Models\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetEmail(): void
    {
        $user = new User('test@email.com', 'TestPassword', '21-03-2022', 2);
        $this->assertSame('test@email.com', $user->getEmail());
    }

    public function testGetPassword(): void
    {
        $user = new User('test@email.com', 'TestPassword', '21-03-2022', 2);
        $this->assertSame('TestPassword', $user->getPassword());
    }

    public function testGetCreatedAt(): void
    {
        $user = new User('test@email.com', 'TestPassword', (new DateTime('now'))->format('d-m-Y'), 2);
        $this->assertSame('22-03-2022', $user->getCreatedAt());
    }

    public function testGetId(): void
    {
        $user = new User('test@email.com', 'TestPassword', '21-03-2022', 2);
        $this->assertSame(2, $user->getId());
    }
}