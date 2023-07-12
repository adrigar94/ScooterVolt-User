<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Application\Find;

use ScooterVolt\UserService\Shared\Application\AuthorizationUser;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserRepository;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserFindByIdService
{
    public function __construct(
        private UserRepository $repository,
        private AuthorizationUser $authorizationSerivice
    ) {
    }

    public function __invoke(UserId $id): ?User
    {
        $user = $this->repository->findById($id);
        if($user){
            $this->hasPermission($user->getEmail()->value());
        }
        return $user;
    }

    private function hasPermission(string $email): void
    {
        if (
            !$this->authorizationSerivice->loggedIs($email) and !$this->authorizationSerivice->isAdmin()
        ) {
            throw new UnauthorizedHttpException('Bearer', 'You do not have permission to get this user');
        }
    }
}
