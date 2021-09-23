<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Http\TezTools\Response\PricesLiveGetResponse200;
use App\Message\UpdatePrices;
use App\Repository\PriceHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UpdatePricesHandler implements MessageHandlerInterface
{
    public function __construct(
        private PriceHistoryRepository $priceHistoryRepository,
        private EntityManagerInterface $em,
        private HttpClientInterface $teztoolsClient,
        private SerializerInterface $serializer
    ) {
    }

    public function __invoke(UpdatePrices $command)
    {
        $tokens     = $this->priceHistoryRepository->findAllTokens();
        $prices     = $this->fetchPricesLive();

        $conn = $this->em->getConnection();

        foreach ($prices->contracts  as $contract) {
            $token = $contract->tokenAddress.(isset($contract->tokenId) ? '_'.$contract->tokenId : '');
            if (\in_array($token, $tokens)) {
                $conn->executeStatement('INSERT INTO price_history(token, timestamp, price) VALUES(?,?,?)', [$contract->identifier, $contract->timestamp, $contract->currentPrice]);
            }
        }
    }

    private function fetchPricesLive(): PricesLiveGetResponse200
    {
        $response = $this->teztoolsClient->request('GET', '/v1/prices-live');

        return $this->serializer->deserialize($response->getContent(), PricesLiveGetResponse200::class, 'json');
    }
}
