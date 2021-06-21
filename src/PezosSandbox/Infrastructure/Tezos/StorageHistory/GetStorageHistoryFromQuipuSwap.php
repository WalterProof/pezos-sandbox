<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Tezos\StorageHistory;

use Bzzhh\Tzkt\Api\ContractsApi;
use Bzzhh\Tzkt\Model\StorageRecord;
use PezosSandbox\Infrastructure\Tezos\Contract;
use PezosSandbox\Infrastructure\Tezos\Decimals;

final class GetStorageHistoryFromQuipuSwap implements GetStorageHistory
{
    private ContractsApi $contractsApi;

    public function __construct(ContractsApi $contractsApi)
    {
        $this->contractsApi = $contractsApi;
    }

    public function getStorageHistory(
        Contract $contract,
        Decimals $decimals,
        ?StorageHistory $snapshot = null
    ): StorageHistory {
        if (!$snapshot) {
            $lastId  = 0;
            $limit   = 1000;
            $history = [];

            do {
                $storage = $this->contractsApi->contractsGetStorageHistory(
                    $contract->asString(),
                    $lastId,
                    $limit
                );

                $c       = \count($storage);
                $history = array_merge(
                    $history,
                    $this->transform($storage, $decimals->asInt())
                );
                $lastId = $c > 0 ? end($storage)['id'] : $lastId;
            } while ($c === $limit);

            return new StorageHistory(array_reverse($history));
        }

        $storage = $this->contractsApi->contractsGetStorageHistory(
            $contract->asString()
        );

        $history = array_filter(
            $this->transform($storage, $decimals->asInt()),
            fn (array $record) => $record['id'] > $snapshot->lastId()
        );

        return $snapshot->append(array_reverse($history));
    }

    private function transform(array $storage, int $decimals): array
    {
        return array_reduce(
            array_map(function (StorageRecord $record) use ($decimals) {
                $tezPool = $record->getValue()['storage']->tez_pool / 1_000_000;
                $tokenPool =
                    $record->getValue()['storage']->token_pool /
                    10 ** $decimals;

                return [
                    $record->getTimestamp()->format('Y-m-d H:i:s') => [
                        'ratio'      => $tokenPool > 0 ? $tezPool / $tokenPool : 0,
                        'tez_pool'   => $tezPool,
                        'token_pool' => $tokenPool,
                        'id'         => $record->getId(),
                    ],
                ];
            }, $storage),
            fn (array $record, array $acc) => array_merge($record, $acc),
            []
        );
    }
}
