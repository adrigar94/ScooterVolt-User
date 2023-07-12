<?php

declare(strict_types=1);

namespace tests\User\Application;

use PHPUnit\Framework\MockObject\MockObject;
use ScooterVolt\UserService\User\Application\Find\UserFindAllService;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserPassword;
use ScooterVolt\UserService\User\Domain\UserRepository;
use ScooterVolt\UserService\User\Domain\UserRoles;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserFindAllServiceTest extends KernelTestCase
{
    private UserRepository|MockObject $repository;
    private UserFindAllService $service;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->repository = $this->createMock(UserRepository::class);
        $this->service = new UserFindAllService($this->repository);
    }

    public function testInvoke(): void
    {
        $user1 = new User(UserId::random(), UserFullname::create('name1', 'surname2'), new UserEmail('name@users.com'), new UserPassword('P@55word'), UserRoles::fromNative(['ROLE_USER']), new \DateTime(), new \DateTime());
        $user2 = new User(UserId::random(), UserFullname::create('name1', 'surname2'), new UserEmail('name@users.com'), new UserPassword('P@55word'), UserRoles::fromNative(['ROLE_USER']), new \DateTime(), new \DateTime());

        $expectedUsers = [$user1, $user2];

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedUsers);

        $users = ($this->service)();

        $this->assertEquals($expectedUsers, $users);
    }
}
