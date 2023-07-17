<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Shared\Domain\Bus\Event;

interface EventBus
{
    public function publish(DomainEvent ...$events): void;
}
