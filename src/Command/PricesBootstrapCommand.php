<?php

declare(strict_types=1);

namespace App\Command;

use App\Http\TezTools\Response\ContractsGetResponse200;
use App\Repository\PriceHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:prices:bootstrap',
    description: 'Add a short description for your command',
)]
class PricesBootstrapCommand extends Command
{
    private const SKIPPED_CONTRACTS = ['KT1QDt84bd4YUfE3ZJQYAu2Ckb7ZYNaWytee_0'];
    private const NB_FIELDS   = 3;
    private const BATCH_LIMIT = self::NB_FIELDS * 10000;

    public function __construct(
        private EntityManagerInterface $em,
        private PriceHistoryRepository $priceHistoryRepository,
        private HttpClientInterface $teztoolsClient,
        private SerializerInterface $serializer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io   = new SymfonyStyle($input, $output);

        $conn      = $this->em->getConnection();
        $contracts = $this->fetchContracts();

        $bootstrapped = $this->priceHistoryRepository->findAllTokens();

        foreach ($contracts as $contract) {
            // skip
            if (in_array($contract->identifier, self::SKIPPED_CONTRACTS)) {
                continue;
            }

            if (\in_array($contract->identifier, $bootstrapped)) {
                continue;
            }

            try {
                $prices = $this->fetchPriceHistory($contract->identifier);
            } catch (\Exception $e) {
                $io->error($e->getMessage());
                continue;
            }

            $params = [];
            foreach ($prices as $price) {
                $params[] = $contract->identifier;
                $params[] = $price['timestamp'];
                $params[] = $price['price'];
            }

            $offset = 0;
            $rest   = \count($params) % self::BATCH_LIMIT;
            do {
                $p   = \array_slice($params, $offset, self::BATCH_LIMIT);
                $sql = 'INSERT INTO price_history(token, timestamp, price) VALUES'
                    . implode(',', array_fill(0, \count($p) / self::NB_FIELDS, '(?, ?, ?)'));
                $conn->executeStatement($sql, $p);
                $offset += self::BATCH_LIMIT;
            } while (\count($p) !== $rest);

            $nbInsertions = \count($params) / 3;

            $io->success(sprintf('Inserted Prices data for %s (%s): %d lines', $contract->symbol, $contract->identifier, $nbInsertions));

            // be a gentleman with the api
            sleep(3);
        }

        $io->success(sprintf('Inserted %d tokens prices', \count($contracts)));

        return Command::SUCCESS;
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

    private function fetchPriceHistory(string $identifier): array
    {
        $url = sprintf('/v1/%s/price-history', $identifier);

        $response = $this->teztoolsClient->request('GET', $url);

        $json = json_decode($response->getContent(), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception(sprintf('Could not decode response from %s: %s', $url, json_last_error_msg()));
        }

        return $json;
    }
}
