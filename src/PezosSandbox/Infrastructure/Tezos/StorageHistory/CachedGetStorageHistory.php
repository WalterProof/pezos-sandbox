<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Tezos\StorageHistory;

use PezosSandbox\Infrastructure\Tezos\Contract;
use PezosSandbox\Infrastructure\Tezos\Decimals;
use Psr\Cache\CacheItemPoolInterface;

final class CachedGetStorageHistory implements GetStorageHistory
{
    private GetStorageHistory $getStorageHistory;
    private CacheItemPoolInterface $storageHistoryCache;
    private ?string $refreshInterval = null;

    public function __construct(
        GetStorageHistory $getStorageHistory,
        CacheItemPoolInterface $storageHistoryCache
    ) {
        $this->getStorageHistory   = $getStorageHistory;
        $this->storageHistoryCache = $storageHistoryCache;
    }

    public function getStorageHistory(
        Contract $contract,
        Decimals $decimals,
        ?StorageHistory $snapshot = null
    ): StorageHistory {
        $cached = $this->storageHistoryCache->getItem($contract->asString());

        if (!$cached->isHit()) {
            $cachedSnapshot = $this->storageHistoryCache->getItem(
                sprintf('%s_backup', $contract->asString())
            );
            $snapshot = $cachedSnapshot->isHit()
                ? $cachedSnapshot->get()
                : null;
            $history = $this->getStorageHistory->getStorageHistory(
                $contract,
                $decimals,
                $snapshot
            );

            $cached
                ->set($history)
                ->expiresAfter(
                    \DateInterval::createFromDateString($this->refreshInterval)
                );
            $cachedSnapshot->set($history);
        }

        return $cached->get();
    }

    public function setRefreshInterval(?string $refreshInterval): void
    {
        $this->refreshInterval = $refreshInterval;
    }
}
