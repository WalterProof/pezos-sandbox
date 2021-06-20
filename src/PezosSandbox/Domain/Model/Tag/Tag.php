<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Tag;

use Doctrine\DBAL\Schema\Schema;
use PezosSandbox\Infrastructure\Mapping;
use TalisOrm\Aggregate;
use TalisOrm\AggregateBehavior;
use TalisOrm\AggregateId;
use TalisOrm\Schema\SpecifiesSchema;

final class Tag implements Aggregate, SpecifiesSchema
{
    use AggregateBehavior;
    use Mapping;

    private TagId $tagId;
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

        $instance->tagId = TagId::fromString(
            self::asString($aggregateState, 'tag_id')
        );
        $instance->label = self::asString($aggregateState, 'label');

        return $instance;
    }

    public static function tableName(): string
    {
        return 'tags';
    }

    public function state(): array
    {
        return [
            'tag_id' => $this->tagId->asString(),
            'label'  => $this->label,
        ];
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public function identifier(): array
    {
        return [
            'tag_id' => $this->tagId->asString(),
        ];
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public static function identifierForQuery(AggregateId $aggregateId): array
    {
        return ['tagId' => (string) $aggregateId];
    }

    public static function specifySchema(Schema $schema): void
    {
        $table = $schema->createTable(self::tableName());

        $table->addColumn('tag_id', 'string')->setNotnull(true);
        $table->setPrimaryKey(['tag_id']);

        $table->addColumn('label', 'string')->setNotnull(true);
        $table->addUniqueIndex(['label']);
    }
}
