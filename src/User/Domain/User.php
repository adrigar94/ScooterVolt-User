<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Domain;

use DateTime;

/***
 * TODO: $password use a VO for password
 */
class User
{
    public function __construct(private UserId $id, private UserName $name, private UserEmail $email, private UserPassword $password, private DateTime $created_at, private DateTime $updated_at)
    {
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function setId(UserId $id): void
    {
        $this->id = $id;
    }

    public function getName(): UserName
    {
        return $this->name;
    }

    public function setName(UserName $name): void
    {
        $this->name = $name;
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

    public function setPassword(UserPassword $password): void
    {
        $this->password = $password;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function equals(self $toCompare): bool
    {
        //TODO Is it necessary to check all fields? or is id, name and email enough?
        return $this->getId() == $toCompare->getId()
            and $this->getName() == $toCompare->getName()
            and $this->getEmail() == $toCompare->getEmail()
            and $this->password == $toCompare->password
            and $this->getCreatedAt() == $toCompare->getCreatedAt()
            and $this->getUpdatedAt() == $toCompare->getUpdatedAt();
    }
}
