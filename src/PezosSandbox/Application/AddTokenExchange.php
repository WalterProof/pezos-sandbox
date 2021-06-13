<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Domain\Model\Exchange\ExchangeId;
use PezosSandbox\Domain\Model\Token\TokenId;

final class AddTokenExchange
{
    private string $tokenId;
    private string $exchangeId;
    private string $contract;

    public function __construct(
        string $tokenId,
        string $exchangeId,
        string $contract
    ) {
        $this->tokenId    = $tokenId;
        $this->exchangeId = $exchangeId;
        $this->contract   = $contract;
    }

    public function tokenId(): TokenId
    {
        return TokenId::fromString($this->tokenId);
    }

    public function exchangeId(): ExchangeId
    {
        return ExchangeId::fromString($this->exchangeId);
    }

    public function contract(): string
    {
        return $this->contract;
    }
}
