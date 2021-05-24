<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210516074525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE members (address VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, registeredAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(address))');
        $this->addSql('CREATE TABLE tokens (address VARCHAR(255) NOT NULL, symbol VARCHAR(255) NOT NULL, decimals INT NOT NULL, address_quipuswap VARCHAR(255) DEFAULT NULL, PRIMARY KEY(address))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE members');
        $this->addSql('DROP TABLE tokens');
    }
}
