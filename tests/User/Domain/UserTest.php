<?php

namespace ScooterVolt\UserService\Tests\User\Domain;

use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserName;
use ScooterVolt\UserService\User\Domain\UserPassword;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function testCreateUserInstance()
    {
        self::bootKernel();

        $userId = UserId::random();
        $userName = new UserName('John Doe');
        $userEmail = new UserEmail('john.doe@example.com');
        $password = new UserPassword('Password123!');
        $createdAt = new \DateTime();
        $updatedAt = new \DateTime();

        $user = new User($userId, $userName, $userEmail, $password, $createdAt, $updatedAt);

        $this->assertInstanceOf(User::class, $user);
    }

    public function testVerifyUserPassword()
    {
        self::bootKernel();

        $passwordString = 'Password123!';

        $userId = UserId::random();
        $userName = new UserName('John Doe');
        $userEmail = new UserEmail('john.doe@example.com');
        $password = new UserPassword($passwordString);
        $createdAt = new \DateTime();
        $updatedAt = new \DateTime();

        $user = new User($userId, $userName, $userEmail, $password, $createdAt, $updatedAt);

        $this->assertTrue($user->validatePassword($passwordString));
    }
}