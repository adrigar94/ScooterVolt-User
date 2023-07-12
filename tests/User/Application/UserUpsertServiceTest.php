<?php

declare(strict_types=1);

namespace Tests\ScooterVolt\UserService\User\Application\Upsert;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use ScooterVolt\UserService\Shared\Application\AuthorizationUser;
use ScooterVolt\UserService\User\Application\Upsert\UserUpsertService;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserPassword;
use ScooterVolt\UserService\User\Domain\UserRepository;
use ScooterVolt\UserService\User\Domain\UserRoles;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserUpsertServiceTest extends KernelTestCase
{
    private UserRepository|MockObject $repository;
    private AuthorizationUser|MockObject $authorizationSerivice;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->repository = $this->createMock(UserRepository::class);
        $this->authorizationSerivice = $this->createMock(AuthorizationUser::class);
    }

    public function testInvokeCreate(): void
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
            ->willReturn(null);

            $this->repository->expects($this->once())
            ->method('save')
            ->with(
                $this->logicalAnd(
                    $this->isInstanceOf(User::class),
                    $this->callback(function (User $user) use ($expectedUser) {    
                        return $user->equals($expectedUser);
                    })
                )
            );
        

        $service = new UserUpsertService($this->repository, $this->authorizationSerivice);

        $result = $service->__invoke($userId, $fullname, $email, $password, $roles);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($expectedUser->getId(), $result->getId());
        $this->assertEquals($expectedUser->getFullname(), $result->getFullname());
        $this->assertEquals($expectedUser->getEmail(), $result->getEmail());
        $this->assertEquals($expectedUser->getPasswordVO(), $result->getPasswordVO());
    }


    public function testInvokeEdit(): void
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
            ->method('save')
            ->with(
                $this->logicalAnd(
                    $this->isInstanceOf(User::class),
                    $this->callback(function (User $user) use ($expectedUser) {    
                        return $user->equals($expectedUser);
                    })
                )
            );

        $this->authorizationSerivice->expects($this->once())
            ->method('loggedIs')
            ->willReturn(true);        

        $service = new UserUpsertService($this->repository, $this->authorizationSerivice);

        $result = $service->__invoke($userId, $fullname, $email, $password, $roles);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($expectedUser->getId(), $result->getId());
        $this->assertEquals($expectedUser->getFullname(), $result->getFullname());
        $this->assertEquals($expectedUser->getEmail(), $result->getEmail());
        $this->assertEquals($expectedUser->getPasswordVO(), $result->getPasswordVO());
    }


    public function testInvokeEditAuthDenied(): void
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

        $service = new UserUpsertService($this->repository, $this->authorizationSerivice);


        $this->expectException(UnauthorizedHttpException::class);

        $result = $service->__invoke($userId, $fullname, $email, $password, $roles);
    }
}