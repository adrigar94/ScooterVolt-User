<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Application\Delete;

use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserRepository;

class UserDeleteService
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(UserId $userId): void
    {
        $this->repository->delete($userId);
        
        //TODO events domain
    }
}