<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Shared\Domain\Bus\Event;

use Adrigar94\ValueObjectCraft\Domain\Uuid\UuidValueObject;

abstract class DomainEvent
{
    public function __construct(
        private readonly string $aggregateId,
        private readonly array $body,
        private ?string $eventId = null,
        private ?\DateTimeImmutable $occurredOn = null
    ) {
        $this->eventId = $eventId ?: UuidValueObject::random()->value();
        $this->occurredOn = $occurredOn ?: new \DateTimeImmutable();
    }

    abstract public static function eventName(): string;

    abstract public function toPrimitives(): array;

    abstract public static function fromPrimitives(
        string $aggregateId,
        array $body,
        ?string $eventId,
        ?\DateTimeImmutable $occurredOn
    ): self;

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function body(): array
    {
        return $this->body;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
