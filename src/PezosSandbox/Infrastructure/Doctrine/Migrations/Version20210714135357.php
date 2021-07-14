<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210714135357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE members ADD address VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX uniq_fa2');
        $this->addSql('DROP INDEX uniq_fa1_2');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE members DROP address');
        $this->addSql('CREATE UNIQUE INDEX uniq_fa2 ON tokens (contract, id) WHERE (id IS NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX uniq_fa1_2 ON tokens (contract) WHERE (id IS NULL)');
    }
}
