<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Tezos\StorageHistory;

use PezosSandbox\Infrastructure\Tezos\Contract;
use PezosSandbox\Infrastructure\Tezos\Decimals;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CachedGetStorageHistory implements GetStorageHistory
{
    private GetStorageHistory $getStorageHistory;
    private CacheInterface $storageHistoryCache;
    private ?string $refreshInterval = null;

    public function __construct(
        GetStorageHistory $getStorageHistory,
        CacheInterface $storageHistoryCache
    ) {
        $this->getStorageHistory   = $getStorageHistory;
        $this->storageHistoryCache = $storageHistoryCache;
    }

    public function getStorageHistory(
        Contract $contract,
        Decimals $decimals,
        ?StorageHistory $snapshot = null
    ): StorageHistory {
        return $this->storageHistoryCache->get($contract->asString(), function (
            ItemInterface $item
        ) use ($contract, $decimals, $snapshot) {
            $item->expiresAfter(
                \DateInterval::createFromDateString($this->refreshInterval)
            );

            $snapshotKey = sprintf('%s_backup', $contract->asString());
            $snapshot = $this->storageHistoryCache->get($snapshotKey, function (
                ItemInterface $item
            ) use ($contract, $decimals) {
                $item->expiresAfter(null);

                return $this->getStorageHistory->getStorageHistory(
                    $contract,
                    $decimals
                );
            });

            $history = $this->getStorageHistory->getStorageHistory(
                $contract,
                $decimals,
                $snapshot
            );

            if ($history->lastId() > $snapshot->lastId()) {
                $this->storageHistoryCache->delete($snapshotKey);
                $this->storageHistoryCache->get($snapshotKey, function (
                    ItemInterface $item
                ) use ($history) {
                    $item->expiresAfter(null);

                    return $history;
                });
            }

            return $history;
        });
    }

    public function setRefreshInterval(?string $refreshInterval): void
    {
        $this->refreshInterval = sprintf('%d seconds', $refreshInterval / 1000);
    }
}
