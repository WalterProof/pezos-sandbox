<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Tezos\StorageHistory;

class StorageHistory
{
    private array $history = [];

    public function __construct(array $history)
    {
        $this->history = $history;
    }

    public function history(): array
    {
        return $this->history;
    }

    public function lastId(): int
    {
        return end($this->history)['id'];
    }

    public function append(array $history): self
    {
        $copy = clone $this;

        $copy->history = array_merge($this->history, $history);

        return $copy;
    }
}
