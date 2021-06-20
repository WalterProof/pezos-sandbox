<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\TalisOrm;

use PezosSandbox\Application\Exchanges\Exchange;
use PezosSandbox\Application\Exchanges\Exchanges;
use PezosSandbox\Domain\Model\Exchange\CouldNotFindExchange;
use PezosSandbox\Domain\Model\Exchange\ExchangeId;
use PezosSandbox\Infrastructure\Doctrine\Connection;
use PezosSandbox\Infrastructure\Doctrine\NoResult;
use PezosSandbox\Infrastructure\Mapping;

final class ExchangesUsingDoctrineDbal implements Exchanges
{
    use Mapping;

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getOneByName(string $name): Exchange
    {
        try {
            $qb = $this->connection
                ->createQueryBuilder()
                ->select('*')
                ->from('exchanges')
                ->andWhere('name = :name')
                ->setParameter('name', $name);

            $data = $this->connection->selectOne($qb);

            return $this->createExchange($data);
        } catch (NoResult $exception) {
            throw CouldNotFindExchange::withName($name);
        }
    }

    public function listExchanges(): array
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('exchanges');

        $records = $this->connection->selectAll($qb);

        return array_map(
            fn (array $record): Exchange => new Exchange(
                ExchangeId::fromString(self::asString($record, 'exchange_id')),
                self::asString($record, 'name'),
                self::asString($record, 'homepage')
            ),
            $records
        );
    }

    /**
     * @param array<string,mixed> $data
     */
    private function createExchange($data): Exchange
    {
        return new Exchange(
            ExchangeId::fromString($data['exchange_id']),
            self::asString($data, 'name'),
            self::asString($data, 'homepage')
        );
    }
}
