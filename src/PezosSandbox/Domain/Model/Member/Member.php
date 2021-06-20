<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Member;

use DateTimeImmutable;
use Doctrine\DBAL\Schema\Schema;
use PezosSandbox\Infrastructure\Mapping;
use TalisOrm\Aggregate;
use TalisOrm\AggregateBehavior;
use TalisOrm\AggregateId;
use TalisOrm\Schema\SpecifiesSchema;

final class Member implements Aggregate, SpecifiesSchema
{
    use AggregateBehavior;
    use Mapping;

    private PubKey $pubKey;
    private bool $wasGrantedAccess = false;
    private DateTimeImmutable $registeredAt;

    public static function requestAccess(
        PubKey $pubKey,
        DateTimeImmutable $registeredAt
    ): self {
        $member = new self();

        $member->pubKey       = $pubKey;
        $member->registeredAt = self::removeMicrosecondsPart($registeredAt);

        return $member;
    }

    public function pubKey(): PubKey
    {
        return $this->pubKey;
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

        $instance->pubKey = PubKey::fromString(
            self::asString($aggregateState, 'pub_key'),
        );

        $instance->registeredAt = self::dateTimeAsDateTimeImmutable(
            $aggregateState,
            'registered_at',
        );

        return $instance;
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public function state(): array
    {
        return [
            'pub_key'       => $this->pubKey->asString(),
            'registered_at' => self::dateTimeImmutableAsDateTimeString(
                $this->registeredAt,
            ),
        ];
    }

    public static function tableName(): string
    {
        return 'members';
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public function identifier(): array
    {
        return [
            'pub_key' => $this->pubKey->asString(),
        ];
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public static function identifierForQuery(AggregateId $aggregateId): array
    {
        return [
            'pub_key' => (string) $aggregateId,
        ];
    }

    public static function specifySchema(Schema $schema): void
    {
        $table = $schema->createTable(self::tableName());

        $table->addColumn('pub_key', 'string')->setNotnull(true);
        $table->addColumn('registered_at', 'datetime')->setNotnull(true);

        $table->setPrimaryKey(['pub_key']);
    }
}
