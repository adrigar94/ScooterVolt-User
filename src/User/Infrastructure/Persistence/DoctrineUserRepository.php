<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\User\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\UserNotFoundException;
use ScooterVolt\UserService\User\Domain\UniqueEmailViolationException;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserPassword;
use ScooterVolt\UserService\User\Domain\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

final class DoctrineUserRepository implements UserRepository
{
    private const TABLE_NAME = 'users';

    public function __construct(private Connection $connection)
    {
    }

    public function findAll(): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME);

        $rows = $queryBuilder->executeQuery()->fetchAllAssociative();

        $users = [];
        foreach ($rows as $row) {
            $users[] = $this->mapRowToUser($row);
        }

        return $users;
    }

    public function findById(UserId $id): ?User
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $id->toNative());

        $row = $queryBuilder->executeQuery()->fetchAssociative();

        return $row ? $this->mapRowToUser($row) : null;
    }


    public function findByEmail(UserEmail $email): ?User
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where('email = :email')
            ->setParameter('email', $email->toNative());

        $row = $queryBuilder->executeQuery()->fetchAssociative();

        return $row ? $this->mapRowToUser($row) : null;
    }

    private function mapRowToUser(array $row): User
    {
        $id = UserId::fromNative($row['id']);
        $fullname = UserFullname::fromNative($row['fullname']);
        $email = UserEmail::fromNative($row['email']);
        $password = UserPassword::fromHashed($row['password']);
        $createdAt = \DateTime::createFromFormat('Y-m-d H:i:s', $row['created_at']);
        $updatedAt = \DateTime::createFromFormat('Y-m-d H:i:s', $row['updated_at']);

        return new User($id, $fullname, $email, $password, $createdAt, $updatedAt);
    }

    public function save(User $user): void
    {
        $data = [
            'id' => $user->getId()->toNative(),
            'fullname' => $user->getFullname()->toNative(),
            'email' => $user->getEmail()->toNative(),
            'password' => $user->getPasswordVO()->toNative(),
            'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        $existingUser = $this->findById($user->getId());

        try {
            if ($existingUser) {
                $this->connection->update(
                    self::TABLE_NAME,
                    $data,
                    ['id' => $user->getId()->toNative()]
                );
            } else {
                $this->connection->insert(self::TABLE_NAME, $data);
            }
        } catch (UniqueConstraintViolationException $e) {
            if (strpos($e->getMessage(), 'users_email_key') !== false) {
                throw new UniqueEmailViolationException($user->getEmail()->toNative());
            }
            throw $e;
        }
    }

    public function delete(UserId $id): void
    {
        $this->connection->delete(self::TABLE_NAME, ['id' => $id->toNative()]);
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the user is not supported
     * @throws UserNotFoundException    if the user is not found
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
    
        $refreshedUser = $this->findById($user->getId());
    
        if (null === $refreshedUser) {
            throw new UserNotFoundException("id", $user->getId()->value());
        }
    
        return $refreshedUser;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @return bool
     */
    function supportsClass(string $class):bool
    {
        return $class === User::class;
    }


    /**
     * Loads the user for the given user identifier (e.g. username or email).
     *
     * This method must throw UserNotFoundException if the user is not found.
     *
     * @throws UserNotFoundException
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $email = UserEmail::fromNative($identifier);
        $user = $this->findByEmail($email);
    
        if (!$user) {
            throw new UserNotFoundException("email", $identifier);
        }
    
        return $user;
    }
}
