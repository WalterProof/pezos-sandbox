<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211113144409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contract (id INT NOT NULL, symbol VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, decimals INT NOT NULL, total_supply VARCHAR(255) NOT NULL, thumbnail_uri VARCHAR(255) DEFAULT NULL, website_link VARCHAR(255) DEFAULT NULL, telegram_link VARCHAR(255) DEFAULT NULL, twitter_link VARCHAR(255) DEFAULT NULL, discord_link VARCHAR(255) DEFAULT NULL, should_prefer_symbol BOOLEAN DEFAULT \'false\' NOT NULL, apps JSON DEFAULT NULL, tags TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN contract.tags IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE price_history (id SERIAL NOT NULL, token VARCHAR(255) NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, price NUMERIC(27, 18) NOT NULL, tezpool NUMERIC(16, 6) NOT NULL, tokenpool NUMERIC(32, 18) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE contract');
        $this->addSql('DROP TABLE price_history');
    }
}
