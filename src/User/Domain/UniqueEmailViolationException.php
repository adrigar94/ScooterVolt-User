<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Domain;

use DomainException;

class UniqueEmailViolationException extends DomainException
{
    public function __construct(string $email, ?\Throwable $previous = null)
    {
        $message = sprintf('Error: The e-mail address %s is already registered.', $email);
        parent::__construct($message, 409, $previous);
    }

}