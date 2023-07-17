<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Application\Upsert;

use ScooterVolt\UserService\Shared\Application\AuthorizationUser;
use ScooterVolt\UserService\Shared\Domain\Bus\Event\DomainEvent;
use ScooterVolt\UserService\Shared\Domain\Bus\Event\EventBus;
use ScooterVolt\UserService\User\Domain\Events\UserUpsertDomainEvent;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserPassword;
use ScooterVolt\UserService\User\Domain\UserRepository;
use ScooterVolt\UserService\User\Domain\UserRole;
use ScooterVolt\UserService\User\Domain\UserRoles;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserUpsertService
{
    public function __construct(
        private UserRepository $repository,
        private AuthorizationUser $authorizationSerivice,
        private EventBus $eventBus
    ) {
    }

    public function __invoke(UserId $userId, UserFullname $fullname, UserEmail $email, UserPassword $password, UserRoles $roles): User
    {

        $user = $this->repository->findById($userId);

        if ($user) {
            $this->hasPermission($email->value());

            $user->setFullName($fullname);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setRolesVO($roles);
            $user->setUpdatedAt(new \DateTime);
        } else {
            $user = new User($userId, $fullname, $email, $password, $roles, new \DateTime, new \DateTime);
        }

        $this->repository->save($user);

        $this->eventBus->publish(
            new UserUpsertDomainEvent(
                $userId->value(),
                $fullname->name()->value(),
                $fullname->surname()->value(),
                $email->value(),
                $roles->toNative()
            )
        );

        return $user;
    }

    private function hasPermission(string $email): void
    {
        if (
            !$this->authorizationSerivice->loggedIs($email) and !$this->authorizationSerivice->isAdmin()
        ) {
            throw new UnauthorizedHttpException('Bearer', 'You do not have permission to edit this user');
        }
    }
}
