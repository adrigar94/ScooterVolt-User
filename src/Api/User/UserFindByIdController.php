<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Api\User;

use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use ScooterVolt\UserService\User\Application\Find\UserFindByIdService;
use ScooterVolt\UserService\User\Domain\UserId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/user/{id}', name: 'user_find', methods: ['GET'])]
#[OA\Tag("User")]
#[OA\Response(
    response: JsonResponse::HTTP_OK,
    description: "User Found",
    content: new OA\JsonContent(
        type: "object",
        properties: [
            new OA\Property(property: "id", type: "string", example: "062714f2-3916-4924-81fc-5ef985d19f5d"),
            new OA\Property(property: "fullname", type: "json", example: "{\"name\":\"John\",\"surname\":\"Doe\"}"),
            new OA\Property(property: "email", type: "string", example: "john@email.com"),
            new OA\Property(property: "created_at", type: "string", format: "date-time"),
            new OA\Property(property: "updated_at", type: "string", format: "date-time"),
        ]
    )
)]
#[OA\Response(
    response: JsonResponse::HTTP_NOT_FOUND,
    description: "User Not Found"
)]
class UserFindByIdController
{
    public function __construct(private UserFindByIdService $finder)
    {
    }

    public function __invoke(Request $request, string $id): Response
    {
        $userId = new UserId($id);

        $user = $this->finder->__invoke($userId);

        if ($user === null) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id'         => $user->getId(),
            'fullname'   => $user->getFullname(),
            'email'      => $user->getEmail(),
            'created_at' => $user->getCreatedAt(),
            'updated_at' => $user->getUpdatedAt(),
        ], JsonResponse::HTTP_OK);
    }
}
