<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Tests\Api\User;

use PHPUnit\Framework\MockObject\MockObject;
use ScooterVolt\UserService\Api\User\UserDeleteController;
use ScooterVolt\UserService\User\Application\Delete\UserDeleteService;
use ScooterVolt\UserService\User\Domain\UserId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserDeleteControllerTest extends KernelTestCase
{
    private UserDeleteController $controller;
    private UserDeleteService|MockObject $deleteService;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->deleteService = $this->createMock(UserDeleteService::class);

        $this->controller = new UserDeleteController($this->deleteService);
    }

    public function testInvoke(): void
    {
        $userId = UserId::random();

        $this->deleteService->expects($this->once())
            ->method('__invoke')
            ->with($userId);

        $request = Request::create("/api/user/$userId", 'DELETE');

        $response = $this->controller->__invoke($request, $userId->toNative());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}