<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Category;

use Doctrine\DBAL\Schema\Schema;
use PezosSandbox\Infrastructure\Mapping;
use TalisOrm\Aggregate;
use TalisOrm\AggregateBehavior;
use TalisOrm\AggregateId;
use TalisOrm\Schema\SpecifiesSchema;

final class Category implements Aggregate, SpecifiesSchema
{
    use AggregateBehavior;
    use Mapping;

    private CategoryId $categoryId;
    private string $label;

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

    /**
     * @param array<string,mixed>         $aggregateState
     * @param array<string,array<object>> $childEntitiesByType
     */
    public static function fromState(
        array $aggregateState,
        array $childEntitiesByType
    ): self {
        $instance = new self();

        $instance->categoryId = CategoryId::fromString(
            self::asString($aggregateState, 'category_id')
        );
        $instance->label = self::asString($aggregateState, 'label');

        return $instance;
    }

    public static function tableName(): string
    {
        return 'categories';
    }

    public function state(): array
    {
        return [
            'category_id' => $this->categoryId->asString(),
            'label'       => $this->label,
        ];
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public function identifier(): array
    {
        return [
            'category_id' => $this->categoryId->asString(),
        ];
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public static function identifierForQuery(AggregateId $aggregateId): array
    {
        return ['categoryId' => (string) $aggregateId];
    }

    public static function specifySchema(Schema $schema): void
    {
        $table = $schema->createTable(self::tableName());

        $table->addColumn('category_id', 'string')->setNotnull(true);
        $table->setPrimaryKey(['category_id']);

        $table->addColumn('label', 'string')->setNotnull(true);
        $table->addUniqueIndex(['label']);
    }
}
