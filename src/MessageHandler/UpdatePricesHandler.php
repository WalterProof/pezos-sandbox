<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Contract;
use App\Http\TezTools\Model\Contract as ModelContract;
use App\Http\TezTools\Response\PricesLiveGetResponse200;
use App\Message\UpdatePrices;
use App\Repository\ContractRepository;
use App\Repository\PriceHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UpdatePricesHandler implements MessageHandlerInterface
{
    public function __construct(
        private PriceHistoryRepository $priceHistoryRepository,
        private ContractRepository $contractRepository,
        private EntityManagerInterface $em,
        private HttpClientInterface $teztoolsClient,
        private SerializerInterface $serializer
    ) {
    }

    public function __invoke(UpdatePrices $command)
    {
        $identifiers = $this->contractRepository->findAllIdentifiers();
        $prices      = $this->fetchPricesLive();

        $conn = $this->em->getConnection();

        $newIdentifiers = [];
        foreach ($prices->contracts  as $contract) {
            $identifier = $contract->tokenAddress.(isset($contract->tokenId) ? '_'.$contract->tokenId : '');
            if (!\in_array($identifier, $identifiers) && !in_array($identifier, $newIdentifiers)) {
                $this->newContract($identifier);
                $newIdentifiers[] = $identifier;
            }

            $conn->executeStatement('INSERT INTO price_history(token, timestamp, price) VALUES(?,?,?)', [$identifier, $contract->timestamp, $contract->currentPrice]);
        }
    }

    private function newContract(string $identifier)
    {
        $response         = $this->teztoolsClient->request('GET', sprintf('/v1/%s/contract', $identifier));
        $objectNormalizer = new ObjectNormalizer();
        $model            = $objectNormalizer->denormalize(
            json_decode($response->getContent(), true),
            ModelContract::class
        );

        $identifier = isset($model->tokenId)
                ? sprintf('%s_%d', $model->tokenAddress, $model->tokenId)
                : $model->tokenAddress;

        $totalSupply =
                (string) ($model->totalSupply / pow(10, $model->decimals));

        $contract = (new Contract())
                ->setIdentifier($identifier)
                ->setSymbol($model->symbol)
                ->setName($model->name)
                ->setType($model->type)
                ->setDecimals($model->decimals)
                ->setTotalSupply($totalSupply)
                ->setThumbnailUri($model->thumbnailUri)
                ->setWebsiteLink($model->websiteLink)
                ->setTwitterLink($model->twitterLink)
                ->setTelegramLink($model->telegramLink)
                ->setDiscordLink($model->discordLink);

        $this->em->persist($contract);
        $this->em->flush();
    }

    private function fetchPricesLive(): PricesLiveGetResponse200
    {
        $response = $this->teztoolsClient->request('GET', '/v1/prices-live');

        return $this->serializer->deserialize($response->getContent(), PricesLiveGetResponse200::class, 'json');
    }
}
