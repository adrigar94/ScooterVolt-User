<?php

declare(strict_types=1);

namespace tests\Api\User;

use PHPUnit\Framework\MockObject\MockObject;
use ScooterVolt\UserService\Api\User\UserFindByIdController;
use ScooterVolt\UserService\User\Application\Find\UserFindByIdService;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserPassword;
use ScooterVolt\UserService\User\Domain\UserRoles;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserFindByIdControllerTest extends KernelTestCase
{
    private UserFindByIdController $controller;
    private UserFindByIdService|MockObject $findService;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->findService = $this->createMock(UserFindByIdService::class);

        $this->controller = new UserFindByIdController($this->findService);
    }

    public function testInvoke(): void
    {
        $userId = UserId::random();
        $fullname = UserFullname::create('John', 'Doe');
        $email = new UserEmail('john@example.com');
        $user = new User($userId, $fullname, $email, new UserPassword('!P@55word'), UserRoles::fromNative(['ROLE_USER']), new \DateTime(), new \DateTime());

        $this->findService->expects($this->once())
            ->method('__invoke')
            ->with($userId)
            ->willReturn($user);

        $request = Request::create("/api/users/$userId", 'GET');

        $response = $this->controller->__invoke($request, $userId->toNative());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame($userId->toNative(), $responseData['id']);
        $this->assertSame($email->toNative(), $responseData['email']);
        $this->assertArrayHasKey('created_at', $responseData);
        $this->assertArrayHasKey('updated_at', $responseData);
    }

    public function testInvokeUserNotFound(): void
    {
        $userId = UserId::random();

        $this->findService->expects($this->once())
            ->method('__invoke')
            ->with($userId)
            ->willReturn(null);

        $request = Request::create("/api/users/$userId", 'GET');

        $response = $this->controller->__invoke($request, $userId->toNative());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}