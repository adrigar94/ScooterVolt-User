<?php

namespace ScooterVolt\UserService\Tests\User\Domain;

use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserName;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function testCreateUserInstance()
    {
        self::bootKernel();

        $userId = UserId::random();
        $userName = new UserName('John Doe');
        $userEmail = new UserEmail('john.doe@example.com');
        $password = 'password123';
        $createdAt = new \DateTime();
        $updatedAt = new \DateTime();

        $user = new User($userId, $userName, $userEmail, $password, $createdAt, $updatedAt);

        $this->assertInstanceOf(User::class, $user);
    }
}