<?php

declare(strict_types=1);

namespace App\Command;

use App\Http\TezTools\Model\Contract as ModelContract;
use App\Repository\ContractRepository;
use App\Repository\PriceHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:play',
    description: 'Debug',
)]
class PlayCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private PriceHistoryRepository $priceHistoryRepository,
        private ContractRepository $contractRepository,
        private SerializerInterface $serializer,
        private HttpClientInterface $teztoolsClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $objectNormalizer = new ObjectNormalizer();
        $response         = $this->teztoolsClient->request('GET', sprintf('/v1/%s/contract', 'KT1Ava7Qm338ZJj83P1ZhNbGkaRZM8N1FsPD_0'));
        $model            = $objectNormalizer->denormalize(json_decode($response->getContent(), true), ModelContract::class);

        dump($model);

        $identifier = isset($model->tokenId)
                ? sprintf('%s_%d', $model->tokenAddress, $model->tokenId)
                : $model->tokenAddress;

        $totalSupply =
                (string) ($model->totalSupply / pow(10, $model->decimals));

        dump($identifier, $totalSupply, $model);

        return Command::SUCCESS;
    }
}
