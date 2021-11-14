<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Contract;
use App\Entity\PriceHistory;
use App\Http\TezTools\Response\ContractsGetResponse200;
use App\Repository\ContractRepository;
use App\Repository\PriceHistoryRepository;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use JsonMachine\JsonMachine;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:bootstrap',
    description: 'Add a short description for your command',
)]
class BootstrapCommand extends Command
{
    private const PRICES_BATCH_LIMIT = 10000;

    private Connection $conn;

    public function __construct(
        private EntityManagerInterface $em,
        private PriceHistoryRepository $priceHistoryRepository,
        private ContractRepository $contractRepository,
        private HttpClientInterface $teztoolsClient,
        private SerializerInterface $serializer,
        private Stopwatch $stopwatch
    ) {
        parent::__construct();

        $this->conn = $this->em->getConnection();
        $this->conn->getConfiguration()->setSQLLogger(null);
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->stopwatch->start('bootstrap');
        $io = new SymfonyStyle($input, $output);

        try {
            $io->info('Destroy all contracts and prices');
            $this->deleteAllFrom('contract', 'price_history');
            $io->info('Bootstrapping contracts');
            $count = $this->bootstrapContracts();
            $io->success(sprintf('Bootstrapped %d contracts', $count));
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $identifiers = $this->contractRepository->findAllIdentifiers();

        foreach ($identifiers as $identifier) {
            try {
                $io->info(sprintf('Bootstrapping prices data for %s', $identifier));
                $this->bootstrapPrices($identifier);
                $io->success('Done');
            } catch (\Exception $e) {
                $io->error($e->getMessage());

                return Command::FAILURE;
            }
        }

        $this->stopwatch->stop('bootstrap');
        $io->info($this->stopwatch->getEvent('bootstrap'));

        return Command::SUCCESS;
    }

    private function bootstrapContracts(): ?int
    {
        $contracts = $this->fetchContracts();
        $count     = 0;

        foreach ($contracts as $c) {
            $contract = (new Contract())
                ->setIdentifier($c->identifier)
                ->setSymbol($c->symbol)
                ->setShouldPreferSymbol($c->shouldPreferSymbol)
                ->setName($c->name)
                ->setType($c->type)
                ->setApps($c->apps)
                ->setTags($c->tags)
                ->setDecimals($c->decimals)
                ->setTotalSupply($c->totalSupply)
                ->setThumbnailUri($c->thumbnailUri)
                ->setWebsiteLink($c->websiteLink)
                ->setTwitterLink($c->twitterLink)
                ->setTelegramLink($c->telegramLink)
                ->setDiscordLink($c->discordLink);
            $this->em->persist($contract);
            ++$count;
        }

        $this->em->flush();
        $this->em->clear();

        return $count;
    }

    private function bootstrapPrices(string $identifier): void
    {
        $jsonChunks = $this->fetchPriceHistory($identifier);

        foreach (JsonMachine::fromIterable($jsonChunks) as $index => $price) {
            $p = (new PriceHistory())
              ->setToken($identifier)
              ->setPrice((string) $price['price'])
              ->setTezpool((string) $price['tezpool'])
              ->setTokenpool((string) $price['tokenpool'])
              ->setTimestamp(new DateTime($price['timestamp']))
            ;

            $this->em->persist($p);

            if ($index % static::PRICES_BATCH_LIMIT) {
                $this->em->flush();
                $this->em->clear();
            }
        }

        $this->em->flush();
        $this->em->clear();
    }

    private function fetchContracts(): array
    {
        $response  = $this->teztoolsClient->request('GET', '/v1/contracts');

        $contracts = $this->serializer->deserialize(
            $response->getContent(),
            ContractsGetResponse200::class,
            'json',
        );

        return $contracts->contracts;
    }

    private function fetchPriceHistory(string $identifier): \Generator
    {
        $url = sprintf('/v1/%s/price-history', $identifier);

        $response = $this->teztoolsClient->request('GET', $url, ['timeout' => 10]);

        foreach ($this->teztoolsClient->stream($response) as $chunk) {
            yield $chunk->getContent();
        }
    }

    private function deleteAllFrom(...$tables)
    {
        foreach ($tables as $name) {
            $this->conn->executeStatement(sprintf('DELETE FROM %s', $name));
        }
    }
}
