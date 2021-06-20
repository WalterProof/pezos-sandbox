<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Exchange;

interface ExchangeRepository
{
    public function save(Exchange $exchange): void;

    public function getById(ExchangeId $exchangeId): Exchange;

    public function nextIdentity(): ExchangeId;
}
