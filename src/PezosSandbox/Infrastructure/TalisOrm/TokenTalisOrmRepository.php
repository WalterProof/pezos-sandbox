<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\TalisOrm;

use Assert\Assert;
use PezosSandbox\Domain\Model\Token\Address;
use PezosSandbox\Domain\Model\Token\CouldNotFindToken;
use PezosSandbox\Domain\Model\Token\Token;
use PezosSandbox\Domain\Model\Token\TokenRepository;
use TalisOrm\AggregateNotFoundException;
use TalisOrm\AggregateRepository;

final class TokenTalisOrmRepository implements TokenRepository
{
    private AggregateRepository $aggregateRepository;

    public function __construct(AggregateRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
    }

    public function save(Token $token): void
    {
        $this->aggregateRepository->save($token);
    }

    public function getByAddress(Address $address): Token
    {
        try {
            $token = $this->aggregateRepository->getById(
                Token::class,
                $address,
            );
            Assert::that($token)->isInstanceOf(Token::class);
            /* @var Token $token */

            return $token;
        } catch (AggregateNotFoundException $exception) {
            throw CouldNotFindToken::withAddress($address);
        }
    }

    public function exists(Address $address): bool
    {
        try {
            $this->getByAddress($address);

            return true;
        } catch (CouldNotFindToken $exception) {
            return false;
        }
    }
}
