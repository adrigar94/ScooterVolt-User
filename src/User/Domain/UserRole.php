<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Domain;

use Adrigar94\ValueObjectCraft\Primitive\Enum\EnumValueObject;

class UserRole extends EnumValueObject
{
    public const USER = 'ROLE_USER';
    public const ADMIN = 'ROLE_ADMIN';

    protected function valueMapping(): array
    {
        return [
            self::USER => 'User',
            self::ADMIN => 'Admin'
        ];
    }
}