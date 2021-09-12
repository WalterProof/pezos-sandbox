<?php

declare(strict_types=1);

namespace App\Http\TezTools\Model;

class PriceHistory extends \ArrayObject
{
    public function __construct(array $data)
    {
        // remap with timestamps as key for convenience
        foreach ($data as $item) {
            $this[$item['timestamp']] = $item;
        }
    }

    /**
     * Returns part of history matching given start date and interval.
     */
    public function byInterval(
        \DateTimeImmutable $currentTime,
        string $interval
    ): array {
        $dateFrom = $currentTime
            ->modify($interval)
            ->format('Y-m-d\TH:i:s.000\Z');

        return array_filter(
            iterator_to_array($this),
            fn (string $timestamp) => $timestamp >= $dateFrom,
            ARRAY_FILTER_USE_KEY
        );
    }
}
