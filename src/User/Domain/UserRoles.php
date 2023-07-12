<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Domain;
use Adrigar94\ValueObjectCraft\Primitive\Enum\EnumArrayValueObject;


class UserRoles extends EnumArrayValueObject
{

    static protected function enumClass(): string
    {
        return UserRole::class;
    }

}