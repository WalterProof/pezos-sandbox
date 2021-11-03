<?php

declare(strict_types=1);

namespace App\Http\TezTools\Response;

use App\Http\TezTools\Model\Contract;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ContractsGetResponse200
{
    public array $contracts;

    public function setContracts(array $contracts): void
    {
        $objectNormalizer = new ObjectNormalizer();

        foreach ($contracts as $data) {
            // skipping bad metadata
            if (!isset($data['symbol']) || !isset($data['totalSupply'])) {
                continue;
            }

            $contract = $objectNormalizer->denormalize($data, Contract::class);

            $contract->identifier = isset($data['tokenId'])
                ? sprintf('%s_%d', $data['tokenAddress'], $data['tokenId'])
                : $data['tokenAddress'];

            $contract->totalSupply =
                (string) ($data['totalSupply'] / pow(10, $data['decimals']));

            $this->contracts[] = $contract;
        }
    }
}
