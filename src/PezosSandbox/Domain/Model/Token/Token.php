<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Token;

use Doctrine\DBAL\Schema\Schema;
use PezosSandbox\Infrastructure\Mapping;
use TalisOrm\Aggregate;
use TalisOrm\AggregateBehavior;
use TalisOrm\AggregateId;
use TalisOrm\Schema\SpecifiesSchema;

final class Token implements Aggregate, SpecifiesSchema
{
    use AggregateBehavior;
    use Mapping;

    const KIND_FA1_2 = 'FA1.2';
    const KIND_FA2   = 'FA2';

    private Address $address;
    private string $addressQuipuswap;
    private string $kind;
    private string $symbol;
    private string $name;
    private int $decimals;
    private ?string $thumbnailUri;
    private ?string $description;
    private ?string $homepage;
    private ?array $social;
    private bool $active = true;

    public function address(): Address
    {
        return $this->address;
    }

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

        $instance->address = Address::fromString(
            self::asString($aggregateState, 'address'),
        );
        $instance->addressQuipuswap = self::asString(
            $aggregateState,
            'address_quipuswap',
        );
        $instance->kind             = self::asString($aggregateState, 'kind');
        $instance->symbol           = self::asString($aggregateState, 'symbol');
        $instance->name             = self::asString($aggregateState, 'name');
        $instance->decimals         = self::asInt($aggregateState, 'decimals');
        $instance->thumbnailUri     = self::asString(
            $aggregateState,
            'thumbnail_uri',
        );
        $instance->description = self::asString($aggregateState, 'description');
        $instance->homepage    = self::asString($aggregateState, 'homepage');
        $instance->social      = self::asArray($aggregateState, 'social');
        $instance->active      = self::asBool($aggregateState, 'active');

        return $instance;
    }

    public static function createToken(
        Address $address,
        string $addressQuipuswap,
        string $kind,
        int $decimals,
        string $symbol,
        string $name,
        ?string $description = null,
        ?string $homepage = null,
        ?array $social = null,
        ?string $thumbnailUri = null,
        bool $active = true
    ): self {
        $token = new self();

        $token->address          = $address;
        $token->addressQuipuswap = $addressQuipuswap;
        $token->kind             = $kind;
        $token->decimals         = $decimals;
        $token->symbol           = $symbol;
        $token->name             = $name;
        $token->description      = $description;
        $token->homepage         = $homepage;
        $token->social           = $social;
        $token->thumbnailUri     = $thumbnailUri;
        $token->active           = $active;

        return $token;
    }

    public function update(
        string $addressQuipuswap,
        string $kind,
        int $decimals,
        string $symbol,
        string $name,
        ?string $description = null,
        ?string $homepage = null,
        ?string $thumbnailUri = null,
        bool $active
    ) {
        $this->addressQuipuswap = $addressQuipuswap;
        $this->kind             = $kind;
        $this->decimals         = $decimals;
        $this->symbol           = $symbol;
        $this->name             = $name;
        $this->description      = $description;
        $this->homepage         = $homepage;
        $this->thumbnailUri     = $thumbnailUri;
        $this->active           = $active;
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public function state(): array
    {
        return [
            'address'           => $this->address->asString(),
            'address_quipuswap' => $this->addressQuipuswap,
            'kind'              => $this->kind,
            'symbol'            => $this->symbol,
            'name'              => $this->name,
            'description'       => $this->description,
            'homepage'          => $this->homepage,
            'social'            => json_encode($this->social),
            'decimals'          => $this->decimals,
            'thumbnail_uri'     => $this->thumbnailUri,
            'active'            => (int) $this->active,
        ];
    }

    public static function tableName(): string
    {
        return 'tokens';
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public function identifier(): array
    {
        return [
            'address' => $this->address,
        ];
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public static function identifierForQuery(AggregateId $aggregateId): array
    {
        return [
            'address' => (string) $aggregateId,
        ];
    }

    public static function specifySchema(Schema $schema): void
    {
        $table = $schema->createTable(self::tableName());

        $table->addColumn('address', 'string')->setNotnull(true);
        $table->setPrimaryKey(['address']);

        $table->addColumn('kind', 'string')->setNotnull(true);
        $table->addColumn('symbol', 'string')->setNotnull(true);
        $table->addColumn('name', 'string')->setNotnull(false);
        $table->addColumn('decimals', 'integer')->setNotnull(true);
        $table->addColumn('address_quipuswap', 'string')->setNotnull(false);
        $table->addColumn('thumbnail_uri', 'string')->setNotNull(false);
        $table->addColumn('homepage', 'string')->setNotNull(false);
        $table->addColumn('description', 'text')->setNotNull(false);
        $table->addColumn('social', 'json')->setNotNull(false);
        $table
            ->addColumn('active', 'boolean')
            ->setNotnull(true)
            ->setDefault(true);
    }
}
