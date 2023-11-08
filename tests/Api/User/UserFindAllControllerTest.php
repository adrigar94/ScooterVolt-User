<?php

declare(strict_types=1);

namespace tests\Api\User;

use PHPUnit\Framework\MockObject\MockObject;
use ScooterVolt\UserService\Api\User\UserFindAllController;
use ScooterVolt\UserService\User\Application\Find\UserFindAllService;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserPassword;
use ScooterVolt\UserService\User\Domain\UserRoles;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserFindAllControllerTest extends KernelTestCase
{
    private UserFindAllController $controller;
    private UserFindAllService|MockObject $findService;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->findService = $this->createMock(UserFindAllService::class);

        $this->controller = new UserFindAllController($this->findService);
    }

    public function testInvoke(): void
    {
        $user1 = new User(UserId::random(), UserFullname::create('John', 'Doe'), new UserEmail('john@example.com'), new UserPassword('!P@55word'), UserRoles::fromNative(['ROLE_USER']), new \DateTime(), new \DateTime());
        $user2 = new User(UserId::random(), UserFullname::create('Jane', 'Smith'), new UserEmail('jane@example.com'), new UserPassword('!P@55word'), UserRoles::fromNative(['ROLE_USER']), new \DateTime(), new \DateTime());
        $users = [$user1, $user2];

        $this->findService->expects($this->once())
            ->method('__invoke')
            ->willReturn($users);

        $request = Request::create('/api/users', \Symfony\Component\HttpFoundation\Request::METHOD_GET);

        $response = $this->controller->__invoke($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());

        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertCount(2, $responseData);

        $this->assertSame($user1->getId()->toNative(), $responseData[0]['id']);
        $this->assertSame($user1->getEmail()->toNative(), $responseData[0]['email']);
        $this->assertArrayHasKey('created_at', $responseData[0]);
        $this->assertArrayHasKey('updated_at', $responseData[0]);

        $this->assertSame($user2->getId()->toNative(), $responseData[1]['id']);
        $this->assertSame($user2->getEmail()->toNative(), $responseData[1]['email']);
        $this->assertArrayHasKey('created_at', $responseData[1]);
        $this->assertArrayHasKey('updated_at', $responseData[1]);
    }
}