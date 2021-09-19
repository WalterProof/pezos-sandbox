<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\UpdatePrices;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:prices:live',
    description: 'Add a short description for your command',
)]
class PricesLiveCommand extends Command
{
    public function __construct(private MessageBusInterface $messageBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        while (true) {
            $io->success('foo');

            $message = new UpdatePrices();
            $this->messageBus->dispatch($message);

            sleep(30);
        }

        return Command::SUCCESS;
    }
}
