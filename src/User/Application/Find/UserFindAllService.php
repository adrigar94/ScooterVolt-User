<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Application\Find;

use ScooterVolt\UserService\Shared\Application\AuthorizationUser;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserRepository;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserFindAllService
{
    public function __construct(
        private UserRepository $repository,
        private AuthorizationUser $authorizationSerivice
    ) {
    }

    /**
     * @return User[]
     */
    public function __invoke(): array
    {
        $this->hasPermission();
        return $this->repository->findAll();
    }


    private function hasPermission(): void
    {
        if (!$this->authorizationSerivice->isAdmin()) {
            throw new UnauthorizedHttpException('Bearer', 'You do not have permission');
        }
    }
}
