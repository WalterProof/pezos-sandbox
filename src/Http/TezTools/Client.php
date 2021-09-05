<?php

declare(strict_types=1);

namespace App\Http\TezTools;

use App\Model\Contract;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
    public function __construct(
        private CacheInterface $cache,
        private HttpClientInterface $teztoolsClient,
        private DenormalizerInterface $denormalizer
    ) {
    }

    public function fetchContracts(): array
    {
        return $this->cache->get('contracts', function (ItemInterface $item) {
            // TODO refresh on demand in admin
            $item->expiresAfter(null);

            $json = $this->teztoolsClient->request('GET', 'contracts')->getContent();
            $decoded = json_decode($json, true);
            $contracts = $decoded['contracts'];

            return array_map(
                fn (array $contract) => $this->denormalizer->denormalize(
                    $contract,
                    Contract::class
                ),
                $contracts
            );
        });
    }

    public function fetchPriceHistory(): array
    {
        return [];
    }
}
