<?php

declare(strict_types=1);

namespace App\User\Domain;

use Adrigar94\ValueObjectCraft\Primitive\String\StringValueObject;

class UserName extends StringValueObject
{
    public function __construct()
    {
        
    }

    protected static function getMinLength(): int
    {
        return 3;
    }

    protected static function getMaxLength(): int
    {
        return 100;
    }

}