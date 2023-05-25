<?php

declare(strict_types=1);

namespace App\User\Domain;

use DateTime;

class User
{
    public function __construct(private UserId $id, private UserName $name, private UserEmail $email, private UserPassword $password, private DateTime $created_at, private DateTime $updated_at)
    {
    }


    public function getId(): UserId
    {
        return $this->id;
    }
    public function setId($id): void
    {
        $this->id = $id;
    }
    public function getName(): UserName
    {
        return $this->name;
    }
    public function setName($name): void
    {
        $this->name = $name;
    }
    public function getEmail(): UserEmail
    {
        return $this->email;
    }
    public function setEmail($email): void
    {
        $this->email = $email;
    }
    public function getPassword(): UserPassword
    {
        return $this->password;
    }
    public function setPassword($password): void
    {
        $this->password = $password;
    }
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }
    public function setCreatedAt($created_at): void
    {
        $this->created_at = $created_at;
    }
    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }
    public function setUpdatedAt($updated_at): void
    {
        $this->updated_at = $updated_at;
    }


    public function equals(self $toCompare): bool
    {
        return $this->getId() == $toCompare->getId()
            and $this->getName() == $toCompare->getName()
            and $this->getEmail() == $toCompare->getEmail()
            and $this->getPassword() == $toCompare->getPassword()
            and $this->getCreatedAt() == $toCompare->getCreatedAt()
            and $this->getUpdatedAt() == $toCompare->getUpdatedAt();
    }
}
