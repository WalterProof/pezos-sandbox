<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure;

use Symfony\Contracts\Cache\CacheInterface;

final class CacheReset
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function reset(array $keys): int
    {
        $results = [];
        foreach ($keys as $key) {
            $results[] = $this->cache->delete($key);
        }

        return array_sum($results);
    }
}
