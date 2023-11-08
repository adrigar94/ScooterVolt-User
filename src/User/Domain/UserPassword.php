<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Domain;

use Adrigar94\ValueObjectCraft\Domain\Password\PasswordValueObject;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

class UserPassword extends PasswordValueObject
{
    public function encode(string $plainPassword): string
    {
        $hasher = new NativePasswordHasher();

        return $hasher->hash($plainPassword);
    }
}
