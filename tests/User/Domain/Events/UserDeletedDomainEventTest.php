<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Tests\User\Domain\Events;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ScooterVolt\UserService\User\Domain\Events\UserDeletedDomainEvent;

class UserDeletedDomainEventTest extends TestCase
{
    public function testEventProperties(): void
    {
        $userId = '1234';
        $email = 'john.doe@example.com';

        $event = new UserDeletedDomainEvent($userId, $email);

        $this->assertSame(
            [
                'id' => $userId,
                'email' => $email,
            ],
            $event->body()
        );
        $this->assertSame('user.deleted', $event::eventName());
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

        $event = UserDeletedDomainEvent::fromPrimitives(
            $userId,
            [
                'id' => $userId,
                'email' => $email,
            ],
            $eventId,
            $occurredOn
        );

        $this->assertSame($userId, $event->aggregateId());
        $this->assertSame(
            [
                'id' => $userId,
                'email' => $email,
            ],
            $event->body()
        );
        $this->assertSame('user.deleted', $event::eventName());
        $this->assertSame($eventId, $event->eventId());
        $this->assertSame($occurredOn, $event->occurredOn());
    }

    public function testToPrimitivesMethod(): void
    {
        $userId = '1234';
        $email = 'john.doe@example.com';
        $eventId = '5678';
        $occurredOn = new DateTimeImmutable();

        $event = new UserDeletedDomainEvent($userId, $email, $eventId, $occurredOn);

        $this->assertSame(
            [
                'aggregateId' => $event->aggregateId(),
                'body' => [
                    'id' => $userId,
                    'email' => $email,
                ],
                'eventId' => $eventId,
                'occurredOn' => $occurredOn,
            ],
            $event->toPrimitives()
        );
    }
}