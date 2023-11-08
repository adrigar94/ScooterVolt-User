<?php

declare(strict_types=1);

namespace tests\User\Application;

use PHPUnit\Framework\MockObject\MockObject;
use ScooterVolt\UserService\Shared\Application\AuthorizationUser;
use ScooterVolt\UserService\User\Application\Find\UserFindByIdService;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserPassword;
use ScooterVolt\UserService\User\Domain\UserRepository;
use ScooterVolt\UserService\User\Domain\UserRoles;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserFindByIdServiceTest extends KernelTestCase
{
    private UserRepository|MockObject $repository;
    private AuthorizationUser|MockObject $authorizationSerivice;
    private UserFindByIdService $service;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->repository = $this->createMock(UserRepository::class);
        $this->authorizationSerivice = $this->createMock(AuthorizationUser::class);
        $this->service = new UserFindByIdService($this->repository, $this->authorizationSerivice);
    }

    public function testInvoke(): void
    {
        $userId = UserId::random();
        $fullname = UserFullname::create('John', 'Doe');
        $email = new UserEmail('john.doe@example.com');
        $password = new UserPassword('P@55word');
        $roles = UserRoles::fromNative(['ROLE_USER']);

        $expectedUser = new User($userId, $fullname, $email, $password, $roles, new \DateTime(), new \DateTime());

        $this->repository->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($expectedUser);

        $this->authorizationSerivice->expects($this->once())
            ->method('loggedIs')
            ->willReturn(true);

        $result = ($this->service)($userId);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($expectedUser->getId(), $result->getId());
        $this->assertEquals($expectedUser->getFullname(), $result->getFullname());
        $this->assertEquals($expectedUser->getEmail(), $result->getEmail());
        $this->assertEquals($expectedUser->getPasswordVO(), $result->getPasswordVO());
    }

    public function testInvokeAuthDenied(): void
    {
        $userId = UserId::random();
        $fullname = UserFullname::create('John', 'Doe');
        $email = new UserEmail('john.doe@example.com');
        $password = new UserPassword('P@55word');
        $roles = UserRoles::fromNative(['ROLE_USER']);

        $expectedUser = new User($userId, $fullname, $email, $password, $roles, new \DateTime(), new \DateTime());

        $this->repository->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($expectedUser);

        $this->authorizationSerivice->expects($this->once())
            ->method('loggedIs')
            ->willReturn(false);

        $this->authorizationSerivice->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $this->expectException(UnauthorizedHttpException::class);

        ($this->service)($userId);
    }
}
