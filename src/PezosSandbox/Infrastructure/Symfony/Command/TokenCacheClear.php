<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Command;

use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Infrastructure\CacheReset;
use PezosSandbox\Infrastructure\Mapping;
use PezosSandbox\Infrastructure\Tezos\Contract;
use PezosSandbox\Infrastructure\Tezos\Decimals;
use PezosSandbox\Infrastructure\Tezos\StorageHistory\GetStorageHistory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TokenCacheClear extends Command
{
    use Mapping;

    private ApplicationInterface $application;
    private GetStorageHistory $getStorageHistory;
    private CacheReset $cacheReset;

    public function __construct(
        ApplicationInterface $application,
        GetStorageHistory $getStorageHistory,
        CacheReset $cacheReset
    ) {
        parent::__construct();

        $this->application       = $application;
        $this->getStorageHistory = $getStorageHistory;
        $this->cacheReset        = $cacheReset;
    }

    protected function configure()
    {
        $this->setName('app:token:cc');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $tokens = $this->application->listTokens();

        foreach ($tokens as $t) {
            $token = $this->application->getOneTokenByAddress(
                $t->address()->asString()
            );

            if (empty($token->exchanges())) {
                continue;
            }

            $keys = [];
            foreach ($token->exchanges() as $exchange) {
                $keys[] = $exchange->contract();
                $keys[] = sprintf('%s_backup', $exchange->contract());
            }

            if (\count($keys) !== $this->cacheReset->reset($keys)) {
                $output->write('there was some error');

                return static::FAILURE;
            }

            $decimals = Decimals::fromInt($token->metadata()['decimals']);
            foreach ($token->exchanges() as $exchange) {
                $contract = Contract::fromString($exchange->contract());
                $this->getStorageHistory->getStorageHistory(
                    $contract,
                    $decimals
                );
            }
        }

        return static::SUCCESS;
    }
}
