<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Tests\User\Domain;

use Adrigar94\ValueObjectCraft\Domain\Name\NameValueObject;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserPassword;
use ScooterVolt\UserService\User\Domain\UserRoles;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function testCreateUserInstance()
    {
        // self::bootKernel();

        $userId = UserId::random();
        $userName = new NameValueObject('John');
        $surname = new NameValueObject('Doe');
        $fullname = new UserFullname($userName, $surname);
        $userEmail = new UserEmail('john.doe@example.com');
        $password = new UserPassword('Password123!');
        $roles = UserRoles::fromNative(['ROLE_USER']);
        $createdAt = new \DateTime();
        $updatedAt = new \DateTime();

        $user = new User($userId, $fullname, $userEmail, $password, $roles, $createdAt, $updatedAt);

        $this->assertInstanceOf(User::class, $user);
    }

    public function testVerifyUserPassword()
    {
        // self::bootKernel();

        $passwordString = 'Password123!';

        $userId = UserId::random();
        $userName = new NameValueObject('John');
        $surname = new NameValueObject('Doe');
        $fullname = new UserFullname($userName, $surname);
        $userEmail = new UserEmail('john.doe@example.com');
        $password = new UserPassword($passwordString);
        $roles = UserRoles::fromNative(['ROLE_USER']);
        $createdAt = new \DateTime();
        $updatedAt = new \DateTime();

        $user = new User($userId, $fullname, $userEmail, $password, $roles, $createdAt, $updatedAt);

        $this->assertTrue($user->validatePassword($passwordString));
    }
}
