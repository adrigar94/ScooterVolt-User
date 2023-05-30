<?php

declare(strict_types=1);

namespace tests\User\Application;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use ScooterVolt\UserService\User\Application\Delete\UserDeleteService;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserDeleteServiceTest extends KernelTestCase
{
    private UserRepository|MockObject $repository;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->repository = $this->createMock(UserRepository::class);
    }

    public function testInvoke(): void
    {
        $userId = UserId::random();

        $this->repository->expects($this->once())
            ->method('delete')
            ->with($this->equalTo($userId));

        $service = new UserDeleteService($this->repository);

        $service->__invoke($userId);

        // No assertions needed, we are only testing the behavior
    }
}
