<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Tests\User\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use ScooterVolt\UserService\User\Domain\UniqueEmailViolationException;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserPassword;
use ScooterVolt\UserService\User\Domain\UserRoles;
use ScooterVolt\UserService\User\Infrastructure\Persistence\DoctrineUserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class DoctrineUserRepositoryTest extends KernelTestCase
{
    private Connection $connection;
    private DoctrineUserRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->connection = static::getContainer()->get(Connection::class);

        $this->setUpDatabase($kernel);

        $this->repository = new DoctrineUserRepository($this->connection);
    }

    public function testFindAll(): void
    {
        $foundUsers = $this->repository->findAll();

        $this->assertIsArray($foundUsers);
        $this->assertInstanceOf(User::class, $foundUsers[0]);
    }

    public function testFindById(): void
    {
        $userId = new UserId('51210494-e320-45da-894f-1a9587a23a1f');

        $foundUser = $this->repository->findById($userId);

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($userId, $foundUser->getId());
    }


    public function testFindByEmail(): void
    {
        $userEmail = new UserEmail('john.doe@example.com');

        $foundUser = $this->repository->findByEmail($userEmail);
        
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($userEmail, $foundUser->getEmail());
    }

    public function testSave(): void
    {
        $userId = new UserId('51210494-e320-45da-894f-1a9587a23a1f');
        $fullname = UserFullname::create('John', 'Doe');
        $email = new UserEmail('john.doe@example.com');
        $password = new UserPassword('!Password123');
        $roles = UserRoles::fromNative(['ROLE_USER']);
        $user = new User($userId, $fullname, $email, $password, $roles, new \DateTime(), new \DateTime());

        $this->repository->save($user);

        $foundUser = $this->repository->findById($userId);

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertTrue($user->equals($foundUser));
    }

    public function testSaveWithDuplicateEmail(): void
    {
        $userId1 = new UserId('51210494-e320-45da-894f-1a9587a23a1f');
        $userId2 = new UserId('51210494-e320-45da-894f-1a9587a23a2f');
        $fullname = UserFullname::create('Isaac', 'Newton');
        $email = new UserEmail('issac.newton@royalsociety.org');
        $password = new UserPassword('!Password123');
        $roles = UserRoles::fromNative(['ROLE_USER']);
        $user1 = new User($userId1, $fullname, $email, $password, $roles, new \DateTime(), new \DateTime());
        $user2 = new User($userId2, $fullname, $email, $password, $roles, new \DateTime(), new \DateTime());

        $this->repository->save($user1);

        $this->expectException(UniqueEmailViolationException::class);

        $this->repository->save($user2);
    }


    public function testDelete(): void
    {
        $userId = new UserId('51210494-e320-45da-894f-1a9587a23a1f');

        $foundUser = $this->repository->findById($userId);
        $this->assertEquals($userId, $foundUser->getId());

        $this->repository->delete($userId);

        $foundUser = $this->repository->findById($userId);
        $this->assertNull($foundUser);
    }

    private function setUpDatabase(KernelInterface $kernel)
    {
        $application = new Application($kernel);

        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:database:drop',
            '--force' => true,
            '--if-exists' => true
        ]);
        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();

        $input = new ArrayInput([
            'command' => 'doctrine:database:create',
        ]);
        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();

        $input = new ArrayInput([
            'command' => 'doctrine:mi:mi',
            '--no-interaction' => true,
        ]);
        $output = new BufferedOutput();
        $application->run($input, $output);
        $output->fetch();

        $sql = file_get_contents('tests/User/Infrastructure/Persistence/testdata.sql');
        $this->connection->executeStatement($sql);
    }
}
