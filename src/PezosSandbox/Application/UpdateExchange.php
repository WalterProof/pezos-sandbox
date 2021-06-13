<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Domain\Model\Exchange\ExchangeId;

final class UpdateExchange
{
    private string $exchangeId;
    private string $name;
    private string $homepage;

    public function __construct(
        string $exchangeId,
        string $name,
        string $homepage
    ) {
        $this->exchangeId = $exchangeId;
        $this->name       = $name;
        $this->homepage   = $homepage;
    }

    public function exchangeId(): ExchangeId
    {
        return ExchangeId::fromString($this->exchangeId);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function homepage(): string
    {
        return $this->homepage;
    }
}
