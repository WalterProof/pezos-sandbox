<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Domain\Model\Exchange\ExchangeId;
use PezosSandbox\Domain\Model\Token\TokenId;

final class RemoveTokenExchange
{
    private string $tokenId;
    private string $exchangeId;

    public function __construct(string $tokenId, string $exchangeId)
    {
        $this->tokenId    = $tokenId;
        $this->exchangeId = $exchangeId;
    }

    public function tokenId(): TokenId
    {
        return TokenId::fromString($this->tokenId);
    }

    public function exchangeId(): ExchangeId
    {
        return ExchangeId::fromString($this->exchangeId);
    }
}
