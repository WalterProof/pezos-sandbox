<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210720211821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE categories (category_id VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(category_id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_3AF34668EA750E8 ON categories (label)'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE categories');
    }
}
