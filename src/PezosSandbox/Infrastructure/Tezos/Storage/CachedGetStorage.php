<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Tezos\Storage;

use PezosSandbox\Infrastructure\Tezos\Contract;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CachedGetStorage implements GetStorage
{
    private GetStorage $getStorage;
    private CacheInterface $storageCache;
    private ?string $refreshInterval = null;

    public function __construct(
        GetStorage $getStorage,
        CacheInterface $storageCache
    ) {
        $this->getStorage   = $getStorage;
        $this->storageCache = $storageCache;
    }

    public function getStorage(Contract $contract): Storage
    {
        return $this->storageCache->get(
            sprintf('%s_storage', $contract->asString()),
            function (ItemInterface $item) use ($contract) {
                $item->expiresAfter(
                    \DateInterval::createFromDateString($this->refreshInterval)
                );

                return $this->getStorage->getStorage($contract);
            }
        );
    }

    public function setRefreshInterval(?string $refreshInterval): void
    {
        $this->refreshInterval = sprintf('%d seconds', $refreshInterval / 1000);
    }
}
