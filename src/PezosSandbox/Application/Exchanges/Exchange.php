<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Exchanges;

use PezosSandbox\Domain\Model\Exchange\ExchangeId;

final class Exchange
{
    private string $exchangeId;
    private string $name;
    private string $homepage;

    public function __construct(
        ExchangeId $exchangeId,
        string $name,
        string $homepage
    ) {
        $this->exchangeId = $exchangeId->asString();
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
