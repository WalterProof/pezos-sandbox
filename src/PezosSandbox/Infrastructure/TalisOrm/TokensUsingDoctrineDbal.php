<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\TalisOrm;

use PezosSandbox\Application\Tokens\Token;
use PezosSandbox\Application\Tokens\Tokens;
use PezosSandbox\Domain\Model\Token\Address;
use PezosSandbox\Domain\Model\Token\CouldNotFindToken;
use PezosSandbox\Infrastructure\Doctrine\Connection;
use PezosSandbox\Infrastructure\Doctrine\NoResult;
use PezosSandbox\Infrastructure\Mapping;

final class TokensUsingDoctrineDbal implements Tokens
{
    use Mapping;

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getOneByAddress(Address $address): Token
    {
        try {
            $data = $this->connection->selectOne(
                $this->connection
                    ->createQueryBuilder()
                    ->select('*')
                    ->from('tokens')
                    ->andWhere('address = :address')
                    ->setParameter('address', $address->asString()),
            );

            return $this->createToken($data);
        } catch (NoResult $exception) {
            throw CouldNotFindToken::withAddress($address);
        }
    }

    public function listTokens(): array
    {
        $records = $this->connection->selectAll(
            $this->connection
                ->createQueryBuilder()
                ->select('*')
                ->from('tokens')
                ->where('active = true')
                ->orderBy('symbol', 'asc'),
        );

        return array_map(
            fn (array $record): Token => new Token(
                self::asString($record, 'address'),
                self::asString($record, 'address_quipuswap'),
                self::asString($record, 'kind'),
                self::asInt($record, 'decimals'),
                self::asString($record, 'symbol'),
                self::asString($record, 'name'),
                self::asString($record, 'description'),
                self::asString($record, 'homepage'),
                self::asString($record, 'thumbnail_uri'),
                self::asBool($record, 'active'),
                self::AsArray($record, 'social'),
                self::asIntOrNull($record, 'supply_adjustment')
            ),
            $records,
        );
    }

    public function listTokensForAdmin(): array
    {
        $records = $this->connection->selectAll(
            $this->connection
                ->createQueryBuilder()
                ->select('*')
                ->from('tokens')
                ->orderBy('symbol', 'asc'),
        );

        return array_map(
            fn (array $record): Token => new Token(
                self::asString($record, 'address'),
                self::asString($record, 'address_quipuswap'),
                self::asString($record, 'kind'),
                self::asInt($record, 'decimals'),
                self::asString($record, 'symbol'),
                self::asString($record, 'name'),
                self::asString($record, 'description'),
                self::asString($record, 'homepage'),
                self::asString($record, 'thumbnail_uri'),
                self::asBool($record, 'active'),
                self::AsArray($record, 'social'),
                self::asIntOrNull($record, 'supply_adjustment')
            ),
            $records,
        );
    }

    /**
     * @param array<string,mixed> $data
     */
    private function createToken($data): Token
    {
        return new Token(
            self::asString($data, 'address'),
            self::asString($data, 'address_quipuswap'),
            self::asString($data, 'kind'),
            self::asInt($data, 'decimals'),
            self::asString($data, 'symbol'),
            self::asString($data, 'name'),
            self::asString($data, 'description'),
            self::asString($data, 'homepage'),
            self::asString($data, 'thumbnail_uri'),
            self::asBool($data, 'active'),
            self::AsArray($data, 'social'),
            self::asIntOrNull($data, 'supply_adjustment')
        );
    }
}
