<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Token;

use Doctrine\DBAL\Schema\Schema;
use PezosSandbox\Domain\Model\Tag\TagId;
use PezosSandbox\Infrastructure\Mapping;
use TalisOrm\AggregateId;
use TalisOrm\ChildEntity;
use TalisOrm\ChildEntityBehavior;
use TalisOrm\Schema\SpecifiesSchema;

final class TokenTag implements ChildEntity, SpecifiesSchema
{
    use ChildEntityBehavior;
    use Mapping;

    private TokenId $tokenId;
    private TagId $tagId;

    private function __construct()
    {
    }

    public function tagId(): TagId
    {
        return $this->tagId;
    }

    public static function create(TokenId $tokenId, TagId $tagId): self
    {
        $tag = new self();

        $tag->tokenId = $tokenId;
        $tag->tagId   = $tagId;

        return $tag;
    }

    /**
     * @param array<string,mixed> $state
     * @param array<string,mixed> $aggregateState
     */
    public static function fromState(array $state, array $aggregateState): self
    {
        $instance = new self();

        $instance->tokenId = TokenId::fromString(
            self::asString($state, 'token_id')
        );
        $instance->tagId = TagId::fromString(self::asString($state, 'tag_id'));

        return $instance;
    }

    /**
     * @return array<string,mixed>
     */
    public function state(): array
    {
        return [
            'token_id' => $this->tokenId->asString(),
            'tag_id'   => $this->tagId->asString(),
        ];
    }

    public static function tableName(): string
    {
        return 'token_tags';
    }

    /**
     * @return array<string,mixed>
     */
    public function identifier(): array
    {
        return [
            'token_id' => $this->tokenId->asString(),
            'tag_id'   => $this->tagId->asString(),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public static function identifierForQuery(AggregateId $aggregateId): array
    {
        return [
            'token_id' => (string) $aggregateId,
        ];
    }

    public static function specifySchema(Schema $schema): void
    {
        $table = $schema->createTable(self::tableName());

        $table->addColumn('token_id', 'string')->setNotnull(true);
        $table->addIndex(['token_id']);

        $table->addColumn('tag_id', 'string')->setNotnull(true);
    }
}
