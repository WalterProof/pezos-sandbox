<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Command;

use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\UpdateToken;
use PezosSandbox\Infrastructure\Mapping;
use PezosSandbox\Infrastructure\Tezos\Contract;
use PezosSandbox\Infrastructure\Tezos\Decimals;
use PezosSandbox\Infrastructure\Tezos\StorageHistory\GetStorageHistory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TokenReorder extends Command
{
    use Mapping;

    private ApplicationInterface $application;
    private GetStorageHistory $getStorageHistory;

    public function __construct(
        ApplicationInterface $application,
        GetStorageHistory $getStorageHistory
    ) {
        parent::__construct();

        $this->application       = $application;
        $this->getStorageHistory = $getStorageHistory;
    }

    protected function configure()
    {
        $this->setName('app:token:reorder');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $tokens = $this->application->listTokens();
        $tezPools = [];

        foreach ($tokens as $t) {
            $token = $this->application->getOneTokenByAddress(
                $t->address()->asString()
            );
            if (empty($token->exchanges)) {
                continue;
            }
            if ($token->isActive()) {
                $history = $this->getStorageHistory
                    ->getStorageHistory(
                        Contract::fromString(
                            $token->exchanges()[0]->contract()
                        ),
                        Decimals::fromInt($token->metadata()['decimals'])
                    )
                    ->history($this->application->getCurrentTime());
                $history = end($history);
                $tezPools[$token->address()->asString()] = self::asInt(
                    $history,
                    'tez_pool'
                );
            }
        }

        arsort($tezPools);
        $tezPools = array_flip($tezPools);
        $position = 0;
        foreach ($tezPools as $address) {
            $position = $position + 1;
            $token = $this->application->getOneTokenByAddress($address);
            $updateToken = new UpdateToken(
                $token->tokenId()->asString(),
                $token->address()->asString(),
                $token->metadata(),
                $token->isActive(),
                $position,
                $token->exchanges()
            );
            $this->application->updateToken($updateToken);
        }

        return static::SUCCESS;
    }
}
