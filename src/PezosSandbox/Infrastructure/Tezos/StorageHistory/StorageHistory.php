<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Tezos\StorageHistory;

use PezosSandbox\Infrastructure\Mapping;

class StorageHistory
{
    use Mapping;

    private array $history = [];
    private array $diff    = [];

    public function __construct(array $history)
    {
        $this->history = $history;
    }

    public function history(
        ?\DateTimeImmutable $currentTime = null,
        ?string $interval = null
    ): array {
        if (!$interval) {
            return $this->history;
        }

        $dateFrom = $this->selectDateFrom($currentTime, $interval);

        return array_filter(
            $this->history,
            fn (string $datetime) => $datetime >= $dateFrom,
            ARRAY_FILTER_USE_KEY
        );
    }

    public function lastId(): int
    {
        return end($this->history)['id'];
    }

    public function append(array $history): self
    {
        $copy = clone $this;

        $copy->history = array_merge($this->history, $history);
        $diff          = array_diff(array_keys($history), array_keys($this->history()));

        $copy->diff = array_map(
            fn (string $key): array => array_merge(
                ['datetime' => $key],
                $history[$key]
            ),
            $diff
        );

        return $copy;
    }

    public function diff(): array
    {
        return $this->diff;
    }

    private function selectDateFrom(
        \DateTimeImmutable $currentTime,
        string $interval
    ) {
        $dateFrom = self::dateTimeImmutableAsDateTimeString(
            $currentTime->modify($interval)
        );

        return $dateFrom;
    }
}
