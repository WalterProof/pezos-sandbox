<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Exchanges;

use PezosSandbox\Domain\Model\Exchange\CouldNotFindExchange;

interface Exchanges
{
    /**
     * @throws CouldNotFindExchange
     */
    public function getOneByName(string $name): Exchange;

    /**
     * @return array<Exchange>
     */
    public function listExchanges(): array;
}
