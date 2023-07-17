<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Tests\User\Domain\Events;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ScooterVolt\UserService\User\Domain\Events\UserUpsertDomainEvent;

class UserUpsertDomainEventTest extends TestCase
{
    public function testEventProperties(): void
    {
        $userId = '1234';
        $name = 'John';
        $surname = 'Doe';
        $email = 'john.doe@example.com';
        $roles = ['ROLE_USER'];

        $event = new UserUpsertDomainEvent($userId, $name, $surname, $email, $roles);

        $this->assertSame(
            [
                'id' => $userId,
                'name' => $name,
                'surname' => $surname,
                'email' => $email,
                'roles' => $roles,
            ],
            $event->body()
        );
        $this->assertSame('user.upsert', $event::eventName());
    }

    public function testFromPrimitivesMethod(): void
    {
        $userId = '1234';
        $name = 'John';
        $surname = 'Doe';
        $email = 'john.doe@example.com';
        $roles = ['ROLE_USER'];
        $eventId = '5678';
        $occurredOn = new DateTimeImmutable();

        $event = UserUpsertDomainEvent::fromPrimitives(
            $userId,
            [
                'id' => $userId,
                'name' => $name,
                'surname' => $surname,
                'email' => $email,
                'roles' => $roles,
            ],
            $eventId,
            $occurredOn
        );

        $this->assertSame($userId, $event->aggregateId());
        $this->assertSame(
            [
                'id' => $userId,
                'name' => $name,
                'surname' => $surname,
                'email' => $email,
                'roles' => $roles,
            ],
            $event->body()
        );
        $this->assertSame('user.upsert', $event::eventName());
        $this->assertSame($eventId, $event->eventId());
        $this->assertSame($occurredOn, $event->occurredOn());
    }

    public function testToPrimitivesMethod(): void
    {
        $userId = '1234';
        $name = 'John';
        $surname = 'Doe';
        $email = 'john.doe@example.com';
        $roles = ['ROLE_USER'];
        $eventId = '5678';
        $occurredOn = new DateTimeImmutable();

        $event = new UserUpsertDomainEvent($userId, $name, $surname, $email, $roles, $eventId, $occurredOn);

        $this->assertSame(
            [
                'aggregateId' => $event->aggregateId(),
                'body' => [
                    'id' => $userId,
                    'name' => $name,
                    'surname' => $surname,
                    'email' => $email,
                    'roles' => $roles
                ],
                'eventId' => $eventId,
                'occurredOn' => $occurredOn,
            ],
            $event->toPrimitives()
        );
    }
}