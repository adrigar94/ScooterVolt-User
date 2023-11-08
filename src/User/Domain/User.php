<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Domain;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private UserId $id,
        private UserFullname $fullName,
        private UserEmail $email,
        private UserPassword $password,
        private UserRoles $roles,
        private \DateTime $created_at,
        private \DateTime $updated_at
    ) {
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function setId(UserId $id): void
    {
        $this->id = $id;
    }

    public function getFullname(): UserFullname
    {
        return $this->fullName;
    }

    public function setFullName(UserFullname $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getEmail(): UserEmail
    {
        return $this->email;
    }

    public function setEmail(UserEmail $email): void
    {
        $this->email = $email;
    }

    public function validatePassword(string $password): bool
    {
        return $this->password->verify($password);
    }

    public function getPasswordVO(): UserPassword
    {
        return $this->password;
    }

    public function getPassword(): string
    {
        return $this->password->value();
    }

    public function setPassword(UserPassword $password): void
    {
        $this->password = $password;
    }

    public function getRoles(): array
    {
        return $this->roles->toNative();
    }

    public function getRolesVO(): UserRoles
    {
        return $this->roles;
    }

    public function setRolesVO(UserRoles $roles): void
    {
        $this->roles = $roles;
    }

    public function eraseCredentials(): void
    {
        // not necessary
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail()->value();
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function equals(self $toCompare): bool
    {
        return $this->getId() == $toCompare->getId() && $this->getFullname() == $toCompare->getFullname() && $this->getEmail() == $toCompare->getEmail() && $this->getPassword() === $toCompare->getPassword();
    }
}
