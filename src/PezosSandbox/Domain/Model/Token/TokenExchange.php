<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Token;

use Doctrine\DBAL\Schema\Schema;
use PezosSandbox\Domain\Model\Exchange\ExchangeId;
use PezosSandbox\Infrastructure\Mapping;
use TalisOrm\AggregateId;
use TalisOrm\ChildEntity;
use TalisOrm\ChildEntityBehavior;
use TalisOrm\Schema\SpecifiesSchema;

final class TokenExchange implements ChildEntity, SpecifiesSchema
{
    use ChildEntityBehavior;
    use Mapping;

    private TokenId $tokenId;
    private ExchangeId $exchangeId;
    private string $contract;

    private function __construct()
    {
    }

    public function exchangeId(): ExchangeId
    {
        return $this->exchangeId;
    }

    public static function create(
        TokenId $tokenId,
        ExchangeId $exchangeId,
        string $contract
    ): self {
        $tokenExchange = new self();

        $tokenExchange->tokenId    = $tokenId;
        $tokenExchange->exchangeId = $exchangeId;
        $tokenExchange->contract   = $contract;

        return $tokenExchange;
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
        $instance->exchangeId = ExchangeId::fromString(
            self::asString($state, 'exchange_id')
        );

        $instance->contract = self::asString($state, 'contract');

        return $instance;
    }

    public function update(string $contract)
    {
        $this->contract = $contract;
    }

    /**
     * @return array<string,mixed>
     */
    public function state(): array
    {
        return [
            'token_id'    => $this->tokenId->asString(),
            'exchange_id' => $this->exchangeId->asString(),
            'contract'    => $this->contract,
        ];
    }

    public static function tableName(): string
    {
        return 'token_exchanges';
    }

    /**
     * @return array<string,mixed>
     */
    public function identifier(): array
    {
        return [
            'token_id'    => $this->tokenId->asString(),
            'exchange_id' => $this->exchangeId->asString(),
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

        $table->addColumn('exchange_id', 'string')->setNotnull(true);
        $table->addColumn('contract', 'string')->setNotnull(true);
    }
}
