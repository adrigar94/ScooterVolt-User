<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Domain;

use Adrigar94\ValueObjectCraft\Domain\Name\FullnameValueObject;
use Adrigar94\ValueObjectCraft\Domain\Name\NameValueObject;

class UserFullname extends FullnameValueObject
{
    public static function create(string $name, string $surname): self
    {
        return new self(new NameValueObject($name), new NameValueObject($surname));
    }
}
