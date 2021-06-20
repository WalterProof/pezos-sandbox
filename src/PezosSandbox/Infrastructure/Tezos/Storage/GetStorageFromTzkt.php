<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Tezos\Storage;

use Bzzhh\Tzkt\Api\ContractsApi;
use PezosSandbox\Infrastructure\Tezos\Contract;

final class GetStorageFromTzkt implements GetStorage
{
    private ContractsApi $contractsApi;

    public function __construct(ContractsApi $contractsApi)
    {
        $this->contractsApi = $contractsApi;
    }

    public function getStorage(Contract $contract): Storage
    {
        $totalSupply = $this->guessTotalSupply($contract->asString());

        return new Storage($totalSupply);
    }

    private function guessTotalSupply(string $contract): ?string
    {
        $storage = json_decode(
            $this->contractsApi->contractsGetStorage($contract)->current()
        );

        if (isset($storage->totalSupply)) {
            return (string) $storage->totalSupply;
        }

        if (isset($storage->total_supply)) {
            return (string) $storage->total_supply;
        }

        if (isset($storage->token) && isset($storage->token->totalSupply)) {
            return (string) $storage->token->totalSupply;
        }

        if (isset($storage->assets) && isset($storage->assets->total_supply)) {
            return (string) $storage->assets->total_supply;
        }

        return null;
    }
}
