<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Api\User;

use ScooterVolt\UserService\User\Application\Find\UserFindAllService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/users', name: 'user_find_all', methods: ['GET'])]
#[OA\Tag("Users")]
#[OA\Response(
    response: JsonResponse::HTTP_OK,
    description: "Users Found",
    content: new OA\JsonContent(
        type: "array",
        items: new OA\Items(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "string", example: "062714f2-3916-4924-81fc-5ef985d19f5d"),
                new OA\Property(property: "fullname", type: "json", example: "{\"name\":\"John\",\"surname\":\"Doe\"}"),
                new OA\Property(property: "email", type: "string", example: "john@email.com"),
                new OA\Property(property: "created_at", type: "string", format: "date-time"),
                new OA\Property(property: "updated_at", type: "string", format: "date-time"),
            ]
        )
    )
)]
class UserFindAllController
{
    public function __construct(private UserFindAllService $finder)
    {
    }

    public function __invoke(Request $request): Response
    {
        $users = ($this->finder)();

        $responseData = [];
        foreach ($users as $user) {
            $responseData[] = [
                'id'         => $user->getId(),
                'fullname'   => $user->getFullname(),
                'email'      => $user->getEmail(),
                'created_at' => $user->getCreatedAt(),
                'updated_at' => $user->getUpdatedAt(),
            ];
        }

        return new JsonResponse($responseData, JsonResponse::HTTP_OK);
    }
}
