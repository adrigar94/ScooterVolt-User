<?php

declare(strict_types=1);

namespace Tests\ScooterVolt\UserService\User\Application\Upsert;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use ScooterVolt\UserService\User\Application\Upsert\UserUpsertService;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserPassword;
use ScooterVolt\UserService\User\Domain\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserUpsertServiceTest extends KernelTestCase
{
    private UserRepository|MockObject $repository;
    private LoggerInterface|MockObject $logger;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->repository = $this->createMock(UserRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testInvoke(): void
    {
        // Mock dependencies

        // Create test data
        $userId = UserId::random();
        $fullname = UserFullname::create('John', 'Doe');
        $email = new UserEmail('john.doe@example.com');
        $password = new UserPassword('P@55word');

        // Create expected user
        $expectedUser = new User($userId, $fullname, $email, $password, new \DateTime(), new \DateTime());

        // Configure mock repository
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


        // Create service instance
        $service = new UserUpsertService($this->repository, $this->logger);

        // Invoke the service
        $result = $service->__invoke($userId, $fullname, $email, $password);

        // Assertions
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($expectedUser->getId(), $result->getId());
        $this->assertEquals($expectedUser->getFullname(), $result->getFullname());
        $this->assertEquals($expectedUser->getEmail(), $result->getEmail());
        $this->assertEquals($expectedUser->getPassword(), $result->getPassword());
    }
}