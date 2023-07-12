<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Application\Upsert;

use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserPassword;
use ScooterVolt\UserService\User\Domain\UserRepository;
use ScooterVolt\UserService\User\Domain\UserRoles;

class UserUpsertService
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(UserId $userId, UserFullname $fullname, UserEmail $email, UserPassword $password, UserRoles $roles): User
    {

        $user = $this->repository->findById($userId);

        if ($user) {
            $user->setFullName($fullname);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setRolesVO($roles);
            $user->setUpdatedAt(new \DateTime);
        } else {
            $user = new User($userId, $fullname, $email, $password, $roles, new \DateTime, new \DateTime);
        }

        $this->repository->save($user);

        //TODO events domain

        return $user;
    }
}
