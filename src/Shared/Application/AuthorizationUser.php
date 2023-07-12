<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Shared\Application;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use ScooterVolt\UserService\User\Domain\UserRole;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthorizationUser
{

    public function __construct(
        private TokenStorageInterface $tokenStorageInterface,
        private JWTTokenManagerInterface $jwtManager
    ) {
    }

    public function isLogged(): bool
    {
        return !is_null($this->tokenStorageInterface->getToken());
    }

    public function decodedToken(): ?array
    {
        if(!$this->isLogged())
        {
            return null;
        }
        return $this->jwtManager->decode($this->tokenStorageInterface->getToken());
    }

    public function loggedHasRole(string $role): ?bool
    {
        if(!$this->isLogged())
        {
            return null;
        }
        $decodedToken = $this->decodedToken();
        return in_array($role, $decodedToken['roles']);
    }

    public function isAdmin(): bool
    {
        if(!$this->isLogged())
        {
            return false;
        }
        return $this->loggedHasRole(UserRole::ADMIN);
    }

    public function loggedIs(string $identifier): ?bool
    {
        if(!$this->isLogged())
        {
            return null;
        }
        $decodedToken = $this->decodedToken();
        return $identifier == $decodedToken['username'];
    }

    public function loggedIsUserAndHasRole(string $identifier, string $role): ?bool
    {
        if(!$this->isLogged())
        {
            return null;
        }
        return $this->loggedIs($identifier) and $this->loggedHasRole($role);
    }
}
