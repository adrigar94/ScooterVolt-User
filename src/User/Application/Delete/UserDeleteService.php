<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Application\Delete;

use ScooterVolt\UserService\Shared\Application\AuthorizationUser;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserRepository;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserDeleteService
{
    public function __construct(
        private UserRepository $repository,
        private AuthorizationUser $authorizationSerivice)
    {
    }

    public function __invoke(UserId $userId): void
    {
        $user = $this->repository->findById($userId);
        if($user){
            $this->hasPermission($user->getEmail()->value());
        }
        $this->repository->delete($userId);
        
        //TODO events domain
    }

    private function hasPermission(string $email): void
    {
        if (
            !$this->authorizationSerivice->loggedIs($email) and !$this->authorizationSerivice->isAdmin()
        ) {
            throw new UnauthorizedHttpException('Bearer', 'You do not have permission to delete this user');
        }
    }
}