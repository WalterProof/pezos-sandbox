<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Tezos\StorageHistory;

use PezosSandbox\Infrastructure\Tezos\Contract;
use PezosSandbox\Infrastructure\Tezos\Decimals;

interface GetStorageHistory
{
    public function getStorageHistory(
        Contract $contract,
        Decimals $decimals,
        ?StorageHistory $snapshot = null
    ): StorageHistory;
}
