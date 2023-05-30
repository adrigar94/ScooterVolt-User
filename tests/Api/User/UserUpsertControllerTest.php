<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Tests\Api\User;

use PHPUnit\Framework\MockObject\MockObject;
use ScooterVolt\UserService\Api\User\UserUpsertController;
use ScooterVolt\UserService\User\Application\Upsert\UserUpsertService;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserPassword;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserUpsertControllerTest extends KernelTestCase
{

    private UserUpsertController $controller;
    private UserUpsertService|MockObject $upsertService;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->upsertService = $this->createMock(UserUpsertService::class);

        $this->controller = new UserUpsertController($this->upsertService);
    }

    public function testInvoke(): void
    {
        $userId = UserId::random();
        $name = "Test";
        $surname = "Controller";
        $email = "test@controller.com";
        $passwd = "P@55word";

        $user = new User(
            $userId,
            UserFullname::create($name, $surname),
            new UserEmail($email),
            new UserPassword($passwd),
            new \DateTime,
            new \DateTime
        );

        $this->upsertService->expects($this->once())
            ->method('__invoke')
            ->willReturn($user);

        $request = Request::create("/api/users/$userId", 'PUT', [], [], [], [], json_encode([
            'name' => $name,
            'surname' => $surname,
            'email' => $email,
            'password' => $passwd
        ]));

        $response = $this->controller->__invoke($request, $userId->toNative());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('fullname', $responseData);
        $this->assertArrayHasKey('email', $responseData);
        $this->assertArrayHasKey('created_at', $responseData);
        $this->assertArrayHasKey('updated_at', $responseData);

        $this->assertSame($userId->toNative(), $responseData['id']);
    }

}