<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\TalisOrm;

use Assert\Assert;
use PezosSandbox\Domain\Model\Token\CouldNotFindToken;
use PezosSandbox\Domain\Model\Token\Token;
use PezosSandbox\Domain\Model\Token\TokenId;
use PezosSandbox\Domain\Model\Token\TokenRepository;
use Ramsey\Uuid\Uuid;
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

    public function getById(TokenId $tokenId): Token
    {
        try {
            $token = $this->aggregateRepository->getById(
                Token::class,
                $tokenId
            );
            Assert::that($token)->isInstanceOf(Token::class);
            /* @var Token $token */

            return $token;
        } catch (AggregateNotFoundException $exception) {
            throw CouldNotFindToken::withId($tokenId);
        }
    }

    public function exists(TokenId $tokenId): bool
    {
        try {
            $this->getById($tokenId);

            return true;
        } catch (CouldNotFindToken $exception) {
            return false;
        }
    }

    public function nextIdentity(): TokenId
    {
        return TokenId::fromString(Uuid::uuid4()->toString());
    }
}
