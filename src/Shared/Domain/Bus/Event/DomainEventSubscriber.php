<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Shared\Domain\Bus\Event;

interface DomainEventSubscriber
{
    public function subscribedTo(): array;
}
