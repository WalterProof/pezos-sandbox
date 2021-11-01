<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211101153747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE contract_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE contract (id INT NOT NULL, symbol VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, decimals INT NOT NULL, total_supply VARCHAR(255) NOT NULL, thumbnail_uri VARCHAR(255) DEFAULT NULL, website_link VARCHAR(255) DEFAULT NULL, telegram_link VARCHAR(255) DEFAULT NULL, twitter_link VARCHAR(255) DEFAULT NULL, discord_link VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE price_history (id SERIAL NOT NULL, token VARCHAR(255) NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, price NUMERIC(27, 18) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE contract_id_seq CASCADE');
        $this->addSql('DROP TABLE contract');
        $this->addSql('DROP TABLE price_history');
    }
}
