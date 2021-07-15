<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\TalisOrm;

use PezosSandbox\Application\Tokens\Token;
use PezosSandbox\Application\Tokens\TokenExchange;
use PezosSandbox\Application\Tokens\TokenTag;
use PezosSandbox\Application\Tokens\Tokens;
use PezosSandbox\Domain\Model\Token\Address;
use PezosSandbox\Domain\Model\Token\CouldNotFindToken;
use PezosSandbox\Domain\Model\Token\TokenId;
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
            $qb = $this->connection
                ->createQueryBuilder()
                ->select('*')
                ->from('tokens')
                ->andWhere('contract = :contract')
                ->setParameter('contract', $address->contract());

            if (null === $address->id()) {
                $qb->andWhere('id IS NULL');
            } else {
                $qb->andWhere('id = :id')->setParameter('id', $address->id());
            }

            $data = $this->connection->selectOne($qb);

            $token = $this->createToken($data);

            return $this->withExchanges($this->withTags($token));
        } catch (NoResult $exception) {
            throw CouldNotFindToken::withAddress($address);
        }
    }

    public function listTokens(bool $onlyActive = true): array
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('tokens')
            ->orderBy('position');

        if ($onlyActive) {
            $qb->where('active = true');
        }

        $records = $this->connection->selectAll($qb);

        return array_map(
            fn(array $record): Token => new Token(
                TokenId::fromString(self::asString($record, 'token_id')),
                Address::fromState(
                    self::asString($record, 'contract'),
                    self::asIntOrNull($record, 'id')
                ),
                self::asArray($record, 'metadata'),
                self::asBool($record, 'active'),
                self::asIntOrNull($record, 'position')
            ),
            $records
        );
    }

    public function listTokensForAdmin(): array
    {
        return $this->listTokens(false);
    }

    /**
     * @param array<string,mixed> $data
     */
    private function createToken($data): Token
    {
        return new Token(
            TokenId::fromString($data['token_id']),
            Address::fromState(
                self::asString($data, 'contract'),
                self::asIntOrNull($data, 'id')
            ),
            self::asArray($data, 'metadata'),
            self::asBool($data, 'active'),
            self::asIntOrNull($data, 'position')
        );
    }

    private function withExchanges(Token $token): Token
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('token_exchanges', 'te')
            ->leftJoin('te', 'exchanges', 'e', 'e.exchange_id = te.exchange_id')
            ->andWhere('token_id = :tokenId')
            ->setParameter('tokenId', $token->tokenId()->asString());

        $records = $this->connection->selectAll($qb);
        $exchanges = array_map(
            fn(array $record): TokenExchange => new TokenExchange(
                self::asString($record, 'exchange_id'),
                self::asString($record, 'name'),
                self::asString($record, 'contract')
            ),
            $records
        );

        return $token->withExchanges($exchanges);
    }

    private function withTags(Token $token): Token
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('token_tags', 'tt')
            ->leftJoin('tt', 'tags', 't', 't.tag_id = tt.tag_id')
            ->andWhere('token_id = :tokenId')
            ->setParameter('tokenId', $token->tokenId()->asString());

        $records = $this->connection->selectAll($qb);
        $tags = array_map(
            fn(array $record): TokenTag => new TokenTag(
                self::asString($record, 'tag_id'),
                self::asString($record, 'label')
            ),
            $records
        );

        return $token->withTags($tags);
    }
}
