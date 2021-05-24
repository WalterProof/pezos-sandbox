<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Command;

use Bzzhh\Tzkt\Api\BigMapsApi;
use Bzzhh\Tzkt\Api\ContractsApi;
use PezosSandbox\Application\ApplicationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Play extends Command
{
    private ApplicationInterface $application;
    private ContractsApi $contractsApi;
    private BigMapsApi $bigMapsApi;

    public function __construct(
        ApplicationInterface $application,
        ContractsApi $contractsApi,
        BigMapsApi $bigMapsApi
    ) {
        parent::__construct();

        $this->application  = $application;
        $this->contractsApi = $contractsApi;
        $this->bigMapsApi   = $bigMapsApi;
    }

    protected function configure()
    {
        $this->setName('play');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        return static::SUCCESS;
    }
}
