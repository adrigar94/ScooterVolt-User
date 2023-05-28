<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Domain;

interface UserRepository
{
    public function save(User $user): void;

    public function findById(UserID $id): ?User;

    public function findByEmail(UserEmail $email): ?User;

    public function deleteUser(UserId $id): void;
}
