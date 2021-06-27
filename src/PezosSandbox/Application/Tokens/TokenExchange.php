<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Tokens;

final class TokenExchange
{
    private string $exchangeId;
    private string $exchangeName;
    private string $contract;

    public function __construct(
        string $exchangeId,
        string $exchangeName,
        string $contract
    ) {
        $this->exchangeId   = $exchangeId;
        $this->exchangeName = $exchangeName;
        $this->contract     = $contract;
    }

    public function exchangeId(): string
    {
        return $this->exchangeId;
    }

    public function exchangeName(): string
    {
        return $this->exchangeName;
    }

    public function contract(): string
    {
        return $this->contract;
    }
}
