<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Command;

use Bzzhh\Tzkt\Api\BigMapsApi;
use Bzzhh\Tzkt\Api\ContractsApi;
use PezosSandbox\Application\AddToken;
use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Domain\Model\Token\Token;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportDex extends Command
{
    private const COL_SYMBOL            = 0;
    private const COL_ADDRESS_QUIPUSWAP = 1;
    private const COL_DECIMALS          = 2;

    private ApplicationInterface $application;
    private ContractsApi $contractsApi;

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
        $this->setName('import');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $count = 0;

        if (
            ($handle = fopen(\dirname(__DIR__, 5).'/docs/dex.csv', 'r')) !==
            false
        ) {
            while (($data = fgetcsv($handle)) !== false) {
                ++$count;

                $dex = json_decode(
                    $this->contractsApi
                        ->contractsGetStorage(
                            $data[static::COL_ADDRESS_QUIPUSWAP],
                        )
                        ->current(),
                );

                $kind = isset($dex->storage->token_id)
                    ? Token::KIND_FA2
                    : Token::KIND_FA1_2;
                $address =
                    Token::KIND_FA1_2 === $kind
                        ? $dex->storage->token_address
                        : sprintf(
                            '%s_%s',
                            $dex->storage->token_address,
                            $dex->storage->token_id,
                        );

                $addToken = new AddToken(
                    $address,
                    $kind,
                    $data[static::COL_SYMBOL],
                    '',
                    \intval($data[static::COL_DECIMALS]),
                    $data[static::COL_ADDRESS_QUIPUSWAP],
                );

                $this->application->addToken($addToken);
            }
            fclose($handle);
        }

        return static::SUCCESS;
    }
}
