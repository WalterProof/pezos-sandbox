<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210831065642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE exchange (id UUID NOT NULL, name VARCHAR(255) NOT NULL, homepage VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN exchange.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE token (id UUID NOT NULL, token_id INT DEFAULT NULL, address VARCHAR(36) NOT NULL, metadata JSON NOT NULL, active BOOLEAN NOT NULL, position INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN token.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE token_exchange (id UUID NOT NULL, exchange_id UUID NOT NULL, token_id UUID NOT NULL, address VARCHAR(36) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A7646DFA68AFD1A0 ON token_exchange (exchange_id)');
        $this->addSql('CREATE INDEX IDX_A7646DFA41DEE7B9 ON token_exchange (token_id)');
        $this->addSql('COMMENT ON COLUMN token_exchange.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN token_exchange.exchange_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN token_exchange.token_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE token_exchange ADD CONSTRAINT FK_A7646DFA68AFD1A0 FOREIGN KEY (exchange_id) REFERENCES exchange (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE token_exchange ADD CONSTRAINT FK_A7646DFA41DEE7B9 FOREIGN KEY (token_id) REFERENCES token (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE token_exchange DROP CONSTRAINT FK_A7646DFA68AFD1A0');
        $this->addSql('ALTER TABLE token_exchange DROP CONSTRAINT FK_A7646DFA41DEE7B9');
        $this->addSql('DROP TABLE exchange');
        $this->addSql('DROP TABLE token');
        $this->addSql('DROP TABLE token_exchange');
    }
}
