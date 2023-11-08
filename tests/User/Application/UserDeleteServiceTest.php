<?php

declare(strict_types=1);

namespace tests\User\Application;

use PHPUnit\Framework\MockObject\MockObject;
use ScooterVolt\UserService\Shared\Application\AuthorizationUser;
use ScooterVolt\UserService\Shared\Domain\Bus\Event\EventBus;
use ScooterVolt\UserService\User\Application\Delete\UserDeleteService;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserPassword;
use ScooterVolt\UserService\User\Domain\UserRepository;
use ScooterVolt\UserService\User\Domain\UserRoles;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserDeleteServiceTest extends KernelTestCase
{
    private UserRepository|MockObject $repository;
    private AuthorizationUser|MockObject $authorizationSerivice;
    private EventBus|MockObject $eventBus;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->repository = $this->createMock(UserRepository::class);
        $this->authorizationSerivice = $this->createMock(AuthorizationUser::class);
        $this->eventBus = $this->createMock(EventBus::class);
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

        $this->repository->expects($this->once())
            ->method('delete')
            ->with($this->equalTo($userId));

        $this->authorizationSerivice->expects($this->once())
            ->method('loggedIs')
            ->willReturn(true);

        $this->eventBus->expects($this->once())
            ->method('publish');

        $service = new UserDeleteService($this->repository, $this->authorizationSerivice, $this->eventBus);

        $service->__invoke($userId);
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

        $this->repository->expects($this->never())
            ->method('delete');

        $this->eventBus->expects($this->never())
            ->method('publish');

        $service = new UserDeleteService($this->repository, $this->authorizationSerivice, $this->eventBus);

        $this->expectException(UnauthorizedHttpException::class);

        $service->__invoke($userId);
    }
}
