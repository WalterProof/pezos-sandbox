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

    private Address $address;
    private string $password;
    private DateTimeImmutable $registeredAt;


    public static function register(
        Address $address,
        DateTimeImmutable $requestedAccessAt
    ): self {
        $member = new self();

        $member->address = $address;
        $member->requestedAccessAt = self::removeMicrosecondsPart($requestedAccessAt);

        $member->events[] = new MemberRegistered($address, $requestedAccessAt);

        return $member;
    }


    public function memberAddress(): Address
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

        $instance->registeredAt = self::dateTimeAsDateTimeImmutable(
            $aggregateState,
            'registeredAt',
        );

        return $instance;
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public function state(): array
    {
        return [
            'address'     => $this->address->asString(),
            'registeredAt' => self::dateTimeImmutableAsDateTimeString(
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
            'address' => $this->address->asString(),
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

        $table->addColumn('password', 'string')->setNotnull(false);
        $table->addColumn('registeredAt', 'datetime')->setNotnull(false);
    }
}
