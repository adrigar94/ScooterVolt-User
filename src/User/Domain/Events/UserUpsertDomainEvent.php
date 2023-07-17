<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Domain\Events;

use DateTimeImmutable;
use ScooterVolt\UserService\Shared\Domain\Bus\Event\DomainEvent;

class UserUpsertDomainEvent extends DomainEvent
{


    public function __construct(
        string $userId,
        string $name,
        string $surname,
        string $email,
        array $roles,
        ?string $eventId = null,
        ?DateTimeImmutable $occurredOn = null
    ) {
        $body = [
            'id' => $userId,
            'name' => $name,
            'surname' => $surname,
            'email' => $email,
            'roles' => $roles
        ];
        $aggregateId = $userId;
        parent::__construct($aggregateId, $body, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'user.upsert';
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
            $body['name'],
            $body['surname'],
            $body['email'],
            $body['roles'],
            $eventId,
            $occurredOn
        );
    }
}
