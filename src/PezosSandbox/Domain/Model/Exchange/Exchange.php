<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Exchange;

use Doctrine\DBAL\Schema\Schema;
use PezosSandbox\Infrastructure\Mapping;
use TalisOrm\Aggregate;
use TalisOrm\AggregateBehavior;
use TalisOrm\AggregateId;
use TalisOrm\Schema\SpecifiesSchema;

final class Exchange implements Aggregate, SpecifiesSchema
{
    use AggregateBehavior;
    use Mapping;

    private ExchangeId $exchangeId;
    private string $name;
    private string $homepage;

    /**
     * @return array<int,class-string>
     */
    public static function childEntityTypes(): array
    {
        return [];
    }

    /**
     * @return array<string,array<object>>
     */
    public function childEntitiesByType(): array
    {
        return [];
    }

    public static function createExchange(
        string $exchangeId,
        string $name,
        string $homepage
    ): self {
        $exchange = new self();

        $exchange->exchangeId = ExchangeId::fromString($exchangeId);
        $exchange->name       = $name;
        $exchange->homepage   = $homepage;

        return $exchange;
    }

    /**
     * @param array<string,mixed>         $aggregateState
     * @param array<string,array<object>> $childEntitiesByType
     */
    public static function fromState(
        array $aggregateState,
        array $childEntitiesByType
    ): self {
        $instance = new self();

        $instance->exchangeId = ExchangeId::fromString(
            self::asString($aggregateState, 'exchange_id')
        );
        $instance->name     = self::asString($aggregateState, 'name');
        $instance->homepage = self::asString($aggregateState, 'homepage');

        return $instance;
    }

    public static function tableName(): string
    {
        return 'exchanges';
    }

    public function state(): array
    {
        return [
            'exchange_id' => $this->exchangeId->asString(),
            'name'        => $this->name,
            'homepage'    => $this->homepage,
        ];
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public function identifier(): array
    {
        return [
            'exchange_id' => $this->exchangeId->asString(),
        ];
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public static function identifierForQuery(AggregateId $aggregateId): array
    {
        return ['exchange_id' => (string) $aggregateId];
    }

    public static function specifySchema(Schema $schema): void
    {
        $table = $schema->createTable(self::tableName());

        $table->addColumn('exchange_id', 'string')->setNotnull(true);
        $table->setPrimaryKey(['exchange_id']);

        $table->addColumn('name', 'string')->setNotnull(true);
        $table->addColumn('homepage', 'string')->setNotnull(true);
    }
}
