<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Member;

use DateTimeImmutable;
use Doctrine\DBAL\Schema\Schema;
use PezosSandbox\Domain\Service\AccessTokenGenerator;
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

    private ?AccessToken $accessToken = null;

    private bool $wasGrantedAccess = false;

    private DateTimeImmutable $requestedAccessAt;

    private function __construct()
    {
    }

    public static function requestAccess(
        Address $address,
        DateTimeImmutable $requestedAccessAt
    ): self {
        $member = new self();

        $member->address           = $address;
        $member->requestedAccessAt = self::removeMicrosecondsPart(
            $requestedAccessAt,
        );

        $member->events[] = new MemberRequestedAccess(
            $address,
            $requestedAccessAt,
        );

        return $member;
    }

    public function grantAccess(): void
    {
        if ($this->wasGrantedAccess) {
            return;
        }

        $this->wasGrantedAccess = true;

        $this->events[] = new AccessWasGrantedToMember($this->address);
    }

    public function memberAddress(): Address
    {
        return $this->address;
    }

    public function generateAccessToken(
        AccessTokenGenerator $accessTokenGenerator
    ): void {
        if (!$this->wasGrantedAccess) {
            throw CouldNotGenerateAccessToken::becauseMemberHasNotBeenGrantedAccessYet($this->address, );
        }

        $this->accessToken = $accessTokenGenerator->generate();

        $this->events[] = new AnAccessTokenWasGenerated(
            $this->address,
            $this->accessToken,
        );
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

        $accessToken           = self::asStringOrNull($aggregateState, 'accessToken');
        $instance->accessToken = \is_string($accessToken)
            ? AccessToken::fromString($accessToken)
            : null;

        $instance->wasGrantedAccess = self::asBool(
            $aggregateState,
            'wasGrantedAccess',
        );
        $instance->requestedAccessAt = self::dateTimeAsDateTimeImmutable(
            $aggregateState,
            'requestedAccessAt',
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
            'accessToken' => $this->accessToken instanceof AccessToken
                    ? $this->accessToken->asString()
                    : null,
            'wasGrantedAccess'  => $this->wasGrantedAccess,
            'requestedAccessAt' => self::dateTimeImmutableAsDateTimeString(
                $this->requestedAccessAt,
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

        $table->addColumn('accessToken', 'string')->setNotnull(false);
        $table
            ->addColumn('wasGrantedAccess', 'boolean')
            ->setNotnull(true)
            ->setDefault(false);
        $table->addColumn('requestedAccessAt', 'datetime')->setNotnull(false);
    }

    public function clearAccessToken(): void
    {
        $this->accessToken = null;
    }
}
