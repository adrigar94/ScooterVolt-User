<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Api\User;

use OpenApi\Attributes as OA;
use ScooterVolt\UserService\User\Application\Delete\UserDeleteService;
use ScooterVolt\UserService\User\Domain\UserId;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users/{id}', name: 'user_delete', methods: ['DELETE'])]
#[OA\Tag('Users')]
#[OA\Response(
    response: Response::HTTP_NO_CONTENT,
    description: 'User Deleted'
)]
class UserDeleteController
{
    public function __construct(
        private readonly UserDeleteService $deleteService
    ) {
    }

    public function __invoke(Request $request, string $id): Response
    {
        $userId = new UserId($id);

        $this->deleteService->__invoke($userId);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
