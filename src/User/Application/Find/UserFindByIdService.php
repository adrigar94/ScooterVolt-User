<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Application\Find;

use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserRepository;

class UserFindByIdService
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(UserId $id): ?User
    {
        return $this->repository->findById($id);
    }
}