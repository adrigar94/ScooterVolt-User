<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Tests\User\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use ScooterVolt\UserService\User\Domain\User;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserFullname;
use ScooterVolt\UserService\User\Domain\UserId;
use ScooterVolt\UserService\User\Domain\UserPassword;
use ScooterVolt\UserService\User\Infrastructure\Persistence\DoctrineUserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class DoctrineUserRepositoryTest extends KernelTestCase
{
    private Connection $connection;
    private DoctrineUserRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->connection = static::getContainer()->get(Connection::class);

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
        $content = $output->fetch();

        $sql = file_get_contents('tests/User/Infrastructure/Persistence/testdata.sql');
        $this->connection->executeStatement($sql);

        $this->repository = new DoctrineUserRepository($this->connection);
    }

    public function testFindById(): void
    {
        $userId = new UserId('51210494-e320-45da-894f-1a9587a23a1f');
        $fullname = UserFullname::create('John', 'Doe');
        $email = new UserEmail('john.doe@example.com');
        $password = new UserPassword('!Password123');
        $user = new User($userId, $fullname, $email, $password, new \DateTime(), new \DateTime());
        $this->repository->save($user);

        $foundUser = $this->repository->findById($userId);

        $this->assertTrue($user->equals($foundUser));
    }
}
