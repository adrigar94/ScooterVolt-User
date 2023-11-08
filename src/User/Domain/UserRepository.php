<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Domain;

use Symfony\Component\Security\Core\User\UserProviderInterface;

interface UserRepository extends UserProviderInterface
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    public function findById(UserID $id): ?User;

    public function findByEmail(UserEmail $email): ?User;

    public function save(User $user): void;

    public function delete(UserId $id): void;
}
