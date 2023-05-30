<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Application\Find;

use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserRepository;

class UserFindAllService
{
    public function __construct(private UserRepository $repository)
    {
    }

    /**
     * @return User[]
     */
    public function __invoke(): array
    {
        return $this->repository->findAll();
    }
}