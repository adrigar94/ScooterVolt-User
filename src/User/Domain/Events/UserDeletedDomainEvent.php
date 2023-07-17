<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Domain\Events;

use DateTimeImmutable;
use ScooterVolt\UserService\Shared\Domain\Bus\Event\DomainEvent;

class UserDeletedDomainEvent extends DomainEvent
{


    public function __construct(
        string $userId,
        string $email,
        ?string $eventId = null,
        ?DateTimeImmutable $occurredOn = null
    ) {
        $body = [
            'id' => $userId,
            'email' => $email
        ];
        $aggregateId = $userId;
        parent::__construct($aggregateId, $body, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'user.deleted';
    }

    public function toPrimitives(): array
    {
        return [
            'aggregateId' => $this->aggregateId(),
            'body' => $this->body(),
            'eventId' => $this->eventId(),
            'occurredOn' => $this->occurredOn(),
        ];
    }

    public static function fromPrimitives(string $aggregateId, array $body, ?string $eventId, ?DateTimeImmutable $occurredOn): self
    {
        return new static(
            $body['id'],
            $body['email'],
            $eventId,
            $occurredOn
        );
    }
}
