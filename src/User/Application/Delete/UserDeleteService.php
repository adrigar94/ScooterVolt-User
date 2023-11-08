<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Application\Delete;

use ScooterVolt\UserService\Shared\Application\AuthorizationUser;
use ScooterVolt\UserService\Shared\Domain\Bus\Event\EventBus;
use ScooterVolt\UserService\User\Domain\Events\UserDeletedDomainEvent;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserRepository;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserDeleteService
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly AuthorizationUser $authorizationSerivice,
        private readonly EventBus $eventBus
    ) {
    }

    public function __invoke(UserId $userId): void
    {
        $user = $this->repository->findById($userId);
        if ($user instanceof \ScooterVolt\UserService\User\Domain\User) {
            $this->hasPermission($user->getEmail()->value());
        }
        $this->repository->delete($userId);

        $this->eventBus->publish(
            new UserDeletedDomainEvent(
                $userId->value(),
                $user->getEmail()->value()
            )
        );
    }

    private function hasPermission(string $email): void
    {
        if (
            !$this->authorizationSerivice->loggedIs($email) && !$this->authorizationSerivice->isAdmin()
        ) {
            throw new UnauthorizedHttpException('Bearer', 'You do not have permission to delete this user');
        }
    }
}
