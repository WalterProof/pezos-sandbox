<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\TalisOrm\TalisOrmBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;
use TalisOrm\Schema\AggregateSchemaProvider;

final class AggregateMigrationsSchemaProvider implements SchemaProvider
{
    private AggregateSchemaProvider $aggregateSchemaProvider;

    public function __construct(
        AggregateSchemaProvider $aggregateSchemaProvider
    ) {
        $this->aggregateSchemaProvider = $aggregateSchemaProvider;
    }

    public function createSchema(): Schema
    {
        return $this->aggregateSchemaProvider->createSchema();
    }
}
