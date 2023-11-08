<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Api\User;

use Adrigar94\ValueObjectCraft\Domain\Name\NameValueObject;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserPassword;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use ScooterVolt\UserService\User\Application\Upsert\UserUpsertService;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserRoles;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/users/{id}', name: 'user_create', methods: ['PUT'])]
#[OA\Tag("Users")]
#[OA\RequestBody(content: new OA\JsonContent(
    type: "object",
    properties: [
        new OA\Property(property: "name", type: "string", example: "John"),
        new OA\Property(property: "surname", type: "string", example: "Doe"),
        new OA\Property(property: "email", type: "string", example: "john@email.com"),
        new OA\Property(property: "password", type: "string", example: "%53cuRe%")
    ]
))]
#[OA\Response(
    response: JsonResponse::HTTP_CREATED,
    description: "User Created",
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
class UserUpsertController
{
    public function __construct(private readonly UserUpsertService $upsert)
    {
    }

    public function __invoke(Request $request, string $id): Response
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $userId   = new UserId($id);
        $fullname = new UserFullname(new NameValueObject($data['name']), new NameValueObject($data['surname']));
        $email    = new UserEmail($data['email']);
        $password = new UserPassword($data['password']);
        $roles = UserRoles::fromNative(['ROLE_USER']);

        $user = $this->upsert->__invoke($userId, $fullname, $email, $password, $roles);

        return new JsonResponse([
            'id'         => $user->getId(),
            'fullname'   => $user->getFullname(),
            'email'      => $user->getEmail(),
            'created_at' => $user->getCreatedAt(),
            'updated_at' => $user->getUpdatedAt(),
        ], JsonResponse::HTTP_CREATED);
    }
}
