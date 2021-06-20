<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\TalisOrm;

use Assert\Assert;
use PezosSandbox\Domain\Model\Exchange\CouldNotFindExchange;
use PezosSandbox\Domain\Model\Exchange\Exchange;
use PezosSandbox\Domain\Model\Exchange\ExchangeId;
use PezosSandbox\Domain\Model\Exchange\ExchangeRepository;
use Ramsey\Uuid\Uuid;
use TalisOrm\AggregateNotFoundException;
use TalisOrm\AggregateRepository;

final class ExchangeTalisOrmRepository implements ExchangeRepository
{
    private AggregateRepository $aggregateRepository;

    public function __construct(AggregateRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
    }

    public function save(Exchange $exchange): void
    {
        $this->aggregateRepository->save($exchange);
    }

    public function getById(ExchangeId $exchangeId): Exchange
    {
        try {
            $exchange = $this->aggregateRepository->getById(
                Exchange::class,
                $exchangeId
            );
            Assert::that($exchange)->isInstanceOf(Exchange::class);
            /* @var Exchange $exchange */

            return $exchange;
        } catch (AggregateNotFoundException $exception) {
            throw CouldNotFindExchange::withId($exchangeId);
        }
    }

    public function nextIdentity(): ExchangeId
    {
        return ExchangeId::fromString(Uuid::uuid4()->toString());
    }
}
