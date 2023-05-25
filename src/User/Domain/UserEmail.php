<?php

declare(strict_types=1);

namespace App\User\Domain;

use Adrigar94\ValueObjectCraft\Domain\Email\EmailValueObject;

class UserEmail extends EmailValueObject
{
    public function __construct()
    {
    }

}