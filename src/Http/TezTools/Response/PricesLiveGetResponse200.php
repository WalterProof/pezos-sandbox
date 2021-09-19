<?php

declare(strict_types=1);

namespace App\Http\TezTools\Response;

use App\Http\TezTools\Model\ContractLive;

class PricesLiveGetResponse200
{
    /**
     * @var ContractLive[]
     */
    public array $contracts;
    public string $block;
    public string $timestamp;
    public string $found;
    public float $xtzusdValue;

    public function setContracts(array $contracts): void
    {
        foreach ($contracts as $contract) {
            if (isset($contract->tokenId)) {
                $contract->identifier = sprintf(
                    '%s_%d',
                    $contract->tokenAddress,
                    $contract->tokenId
                );
            } else {
                $contract->identifier = $contract->tokenAddress;
            }

            $this->contracts[] = $contract;
        }
    }
}
