<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Token;

use Assert\Assert;
use Doctrine\DBAL\Schema\Schema;
use PezosSandbox\Domain\Model\Exchange\ExchangeId;
use PezosSandbox\Domain\Model\Tag\TagId;
use PezosSandbox\Infrastructure\Mapping;
use TalisOrm\Aggregate;
use TalisOrm\AggregateBehavior;
use TalisOrm\AggregateId;
use TalisOrm\Schema\SpecifiesSchema;

final class Token implements Aggregate, SpecifiesSchema
{
    use AggregateBehavior;
    use Mapping;

    private TokenId $tokenId;
    private Address $address;
    private array $metadata;
    private bool $active;
    private ?int $position   = null;
    private array $tags      = [];
    private array $exchanges = [];

    public function updateExchange(ExchangeId $exchangeId, string $contract)
    {
        foreach ($this->exchanges as $exchange) {
            /** @var TokenExchange $exchange */
            if ($exchange->exchangeId()->equals($exchangeId)) {
                $exchange->update($contract);
            }
        }
    }

    public function addExchange(ExchangeId $exchangeId, string $contract)
    {
        $this->exchanges[] = TokenExchange::create(
            $this->tokenId,
            $exchangeId,
            $contract
        );
    }

    public function removeExchange(ExchangeId $exchangeId)
    {
        foreach ($this->exchanges as $key => $exchange) {
            /** @var TokenExchange $exchange */
            if ($exchange->exchangeId()->equals($exchangeId)) {
                unset($this->exchanges[$key]);
                $this->deleteChildEntity($exchange);
            }
        }
    }

    public function addTag(TagId $tagId)
    {
        $this->tags[] = TokenTag::create(
            $this->tokenId,
            $tagId
        );
    }

    public function removeTag(TagId $tagId)
    {
        foreach ($this->tags as $key => $tag) {
            /** @var TokenTag $tag */
            if ($tag->tagId()->equals($tagId)) {
                unset($this->tag[$key]);
                $this->deleteChildEntity($tag);
            }
        }
    }

    /**
     * @return array<int,class-string>
     */
    public static function childEntityTypes(): array
    {
        return [TokenTag::class, TokenExchange::class];
    }

    /**
     * @return array<string,array<object>>
     */
    public function childEntitiesByType(): array
    {
        return [
            TokenTag::class      => $this->tags,
            TokenExchange::class => $this->exchanges,
        ];
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

        $instance->tokenId = TokenId::fromString(
            self::asString($aggregateState, 'token_id')
        );
        $instance->address = Address::fromState(
            self::asString($aggregateState, 'contract'),
            self::asIntOrNull($aggregateState, 'id')
        );
        $instance->metadata = self::asArray($aggregateState, 'metadata');
        $instance->active   = self::asBool($aggregateState, 'active');
        $instance->position = self::asIntOrNull($aggregateState, 'position');

        $tags = $childEntitiesByType[TokenTag::class];
        Assert::that($tags)
            ->all()
            ->isInstanceOf(TokenTag::class);
        $instance->tags = $tags;

        $exchanges = $childEntitiesByType[TokenExchange::class];
        Assert::that($exchanges)
            ->all()
            ->isInstanceOf(TokenExchange::class);
        $instance->exchanges = $exchanges;

        return $instance;
    }

    public static function createToken(
        TokenId $tokenId,
        Address $address,
        array $metadata,
        bool $active
    ): self {
        $token = new self();

        $token->tokenId  = $tokenId;
        $token->address  = $address;
        $token->metadata = $metadata;
        $token->active   = $active;

        return $token;
    }

    public function toggle(): void
    {
        $this->active = !$this->active;
    }

    public function update(
        Address $address,
        array $metadata,
        bool $active,
        ?int $position
    ) {
        $this->address  = $address;
        $this->metadata = $metadata;
        $this->active   = $active;
        $this->position = $position;
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public function state(): array
    {
        return [
            'token_id' => $this->tokenId->asString(),
            'contract' => $this->address->contract(),
            'id'       => $this->address->id(),
            'metadata' => json_encode($this->metadata),
            'active'   => (int) $this->active,
            'position' => $this->position,
        ];
    }

    public static function tableName(): string
    {
        return 'tokens';
    }

    /**
     * @return array<string,mixed>
     */
    public function identifier(): array
    {
        return [
            'token_id' => $this->tokenId->asString(),
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
        $table->setPrimaryKey(['token_id']);

        $table->addColumn('contract', 'string')->setNotnull(true);
        $table->addColumn('id', 'integer')->setNotnull(false);

        $table->addColumn('metadata', 'text')->setNotNull(true);
        $table->addColumn('position', 'integer')->setNotNull(false);
        $table
            ->addColumn('active', 'boolean')
            ->setNotnull(true)
            ->setDefault(true);
    }
}
