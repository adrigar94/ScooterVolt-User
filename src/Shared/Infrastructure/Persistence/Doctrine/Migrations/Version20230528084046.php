<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230528084046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create table USERS';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE users (id UUID NOT NULL, 
            fullname JSON NOT NULL, 
            email VARCHAR(256) NOT NULL UNIQUE, 
            password VARCHAR(256) NOT NULL, 
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id))'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users');
    }
}
