<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Domain;

use Adrigar94\ValueObjectCraft\Primitive\Enum\EnumValueObject;

class UserRole extends EnumValueObject
{
    protected function valueMapping(): array
    {
        return [
            'ROLE_USER' => 'User',
            'ROLE_ADMIN' => 'Admin'
        ];
    }
}