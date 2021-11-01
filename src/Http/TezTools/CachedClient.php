<?php

declare(strict_types=1);

namespace App\Http\TezTools;

use App\Http\TezTools\Model\PriceHistory;
use App\Http\TezTools\Response\ContractsGetResponse200;
use App\Http\TezTools\Response\XtzPriceGetResponse200;
use DateInterval;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CachedClient
{
    public function __construct(
        private CacheInterface $cache,
        private HttpClientInterface $teztoolsClient,
        private SerializerInterface $serializer
    ) {
    }

    public function fetchContracts(): array
    {
        return $this->cache->get('tt_contracts', function (ItemInterface $item) {
            $item->expiresAfter(new DateInterval('PT1H'));

            $response  = $this->teztoolsClient->request('GET', '/v1/contracts');

            $contracts = $this->serializer->deserialize(
                $response->getContent(),
                ContractsGetResponse200::class,
                'json',
            );

            return $contracts->contracts;
        });
    }

    public function fetchXtzPrice(): XtzPriceGetResponse200
    {
        return $this->cache->get('tt_price', function (ItemInterface $item) {
            $item->expiresAfter(null);

            $response  = $this->teztoolsClient->request('GET', '/v1/xtz-price');

            return $this->serializer->deserialize(
                $response->getContent(),
                XtzPriceGetResponse200::class,
                'json',
            );
        });
    }

    public function fetchPriceHistory(string $identifier): PriceHistory
    {
        return $this->cache->get(sprintf('tt_history_%s', $identifier), function (ItemInterface $item) use ($identifier) {
            $item->expiresAfter(null);

            $response = $this->teztoolsClient->request(
                'GET',
                sprintf('/v1/%s/price-history', $identifier)
            );

            $data = json_decode($response->getContent(), true);

            return new PriceHistory($data);
        });
    }
}
