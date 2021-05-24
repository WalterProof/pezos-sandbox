<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210523071258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE members DROP CONSTRAINT "members_pkey"');
        $this->addSql('ALTER TABLE members ADD pub_key VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE members ADD was_granted_access BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE members DROP address');
        $this->addSql('ALTER TABLE members DROP password');
        $this->addSql('ALTER TABLE members RENAME COLUMN registeredat TO registered_at');
        $this->addSql('ALTER TABLE members ADD PRIMARY KEY (pub_key)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX members_pkey');
        $this->addSql('ALTER TABLE members ADD password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE members DROP was_granted_access');
        $this->addSql('ALTER TABLE members RENAME COLUMN pub_key TO address');
        $this->addSql('ALTER TABLE members RENAME COLUMN registered_at TO registeredat');
        $this->addSql('ALTER TABLE members ADD PRIMARY KEY (address)');
    }
}
