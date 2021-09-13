<?php

declare(strict_types=1);

namespace App\Command;

use App\Http\TezTools\CachedClient;
use App\Http\TezTools\Model\Block;
use App\Http\TezTools\Model\Contract;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:update-prices-history',
    description: 'Add a short description for your command',
)]
class UpdatePricesHistoryCommand extends Command
{
    public function __construct(
        private HttpClientInterface $teztoolsClient,
        private CachedClient $cachedClient,
        private SerializerInterface $serializer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // TODO use messenger and dispatch messages to do things async
        $io   = new SymfonyStyle($input, $output);

        $response = $this->teztoolsClient->request('GET', '/v1/blocks-live');
        $block    = $this->serializer->deserialize($response->getContent(), Block::class, 'json');

        $contracts                = $this->cachedClient->fetchContracts();
        $tokensWithOperations     = [];

        foreach ($contracts as $contract) {
            /** @var Contract $contract */
            if (\in_array($contract->address, $block->operations)) {
                $tokensWithOperations[] = $contract->identifier;
            }
        }

        $tokensWithOperations = array_unique($tokensWithOperations);

        return Command::SUCCESS;
    }
}
