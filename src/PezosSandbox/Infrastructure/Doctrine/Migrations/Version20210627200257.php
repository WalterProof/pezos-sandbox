<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @see https://www.enterprisedb.com/postgres-tutorials/postgresql-unique-constraint-null-allowing-only-one-null
 */
final class Version20210627200257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'unique constraint null: Allowing only one Null';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_AA5A118EE98F2859BF396750');
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_FA1_2 ON tokens (contract, (id IS NULL)) WHERE id IS NULL'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_FA2 ON tokens (contract, id) WHERE id IS NOT NULL'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_AA5A118EE98F2859BF396750');
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_AA5A118EE98F2859BF396750 ON tokens (contract, id)'
        );
    }
}
