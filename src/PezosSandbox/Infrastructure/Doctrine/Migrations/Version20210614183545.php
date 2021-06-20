<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210614183545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE exchanges (exchange_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, homepage VARCHAR(255) NOT NULL, PRIMARY KEY(exchange_id))');
        $this->addSql('CREATE TABLE members (pub_key VARCHAR(255) NOT NULL, registered_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(pub_key))');
        $this->addSql('CREATE TABLE tags (tag_id VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(tag_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6FBC9426EA750E8 ON tags (label)');
        $this->addSql('CREATE TABLE tokens (token_id VARCHAR(255) NOT NULL, contract VARCHAR(255) NOT NULL, id INT DEFAULT NULL, metadata TEXT NOT NULL, active BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(token_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AA5A118EE98F2859BF396750 ON tokens (contract, id)');
        $this->addSql('CREATE TABLE token_exchanges (token_id VARCHAR(255) NOT NULL, exchange_id VARCHAR(255) NOT NULL, contract VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_46C1B06441DEE7B9 ON token_exchanges (token_id)');
        $this->addSql('CREATE TABLE token_tags (token_id VARCHAR(255) NOT NULL, tag_id VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_DB8A14441DEE7B9 ON token_tags (token_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE exchanges');
        $this->addSql('DROP TABLE members');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE tokens');
        $this->addSql('DROP TABLE token_exchanges');
        $this->addSql('DROP TABLE token_tags');
    }
}
