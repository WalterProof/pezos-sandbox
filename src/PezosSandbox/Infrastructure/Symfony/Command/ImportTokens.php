<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Command;

use PezosSandbox\Application\AddExchange;
use PezosSandbox\Application\AddToken;
use PezosSandbox\Application\AddTokenExchange;
use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\UpdateToken;
use PezosSandbox\Infrastructure\Mapping;
use PezosSandbox\Infrastructure\Tezos\Contract;
use PezosSandbox\Infrastructure\Tezos\Decimals;
use PezosSandbox\Infrastructure\Tezos\StorageHistory\GetStorageHistory;
use function Safe\json_decode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportTokens extends Command
{
    use Mapping;

    private ApplicationInterface $application;
    private GetStorageHistory $getStorageHistory;
    private array $headers  = [];
    private array $tezPools = [];

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
        $this->setName('import');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $addExchange = new AddExchange(
            'QuipuSwap',
            'https://quipuswap.com/swap'
        );
        $this->application->addExchange($addExchange);

        $quipu = $this->application->getOneExchangeByName('QuipuSwap');

        $count = 0;

        if (
            ($handle = fopen(
                \dirname(__DIR__, 5).'/data/tokens.csv',
                'r'
            )) !== false
        ) {
            while (($data = fgetcsv($handle)) !== false) {
                ++$count;

                if (1 === $count) {
                    $this->headers = $data;
                    continue;
                }

                $data   = array_combine($this->headers, $data);
                $social = json_decode($data['social'], true);

                $addToken = new AddToken($data['address']);
                $this->application->addToken($addToken);

                $metadata = array_filter(
                    [
                        'decimals'    => self::asIntOrNull($data, 'decimals'),
                        'supply'      => self::asIntOrNull($data, 'supply'),
                        'symbol'      => self::asStringOrNull($data, 'symbol'),
                        'name'        => self::asStringOrNull($data, 'name'),
                        'description' => self::asStringOrNull(
                            $data,
                            'description'
                        ),
                        'homepage'      => self::asStringOrNull($data, 'homepage'),
                        'thumbnail_uri' => self::asStringOrNull(
                            $data,
                            'thumbnail_uri'
                        ),
                        'twitter'   => self::asStringOrNull($social, 'twitter'),
                        'telegram'  => self::asStringOrNull($social, 'telegram'),
                        'discord'   => self::asStringOrNull($social, 'discord'),
                        'reddit'    => self::asStringOrNull($social, 'reddit'),
                        'facebook'  => self::asStringOrNull($social, 'facebook'),
                        'github'    => self::asStringOrNull($social, 'github'),
                        'instagram' => self::asStringOrNull(
                            $social,
                            'instagram'
                        ),
                    ],
                    fn ($item) => null !== $item
                );

                $token = $this->application->getOneTokenByAddress(
                    $data['address']
                );

                $updateToken = new UpdateToken(
                    $token->tokenId()->asString(),
                    $data['address'],
                    $metadata,
                    't' === $data['active'] ? true : false
                );
                $this->application->updateToken($updateToken);

                $addTokenExchange = new AddTokenExchange(
                    $token->tokenId()->asString(),
                    $quipu->exchangeId()->asString(),
                    $data['address_quipuswap']
                );

                $this->application->addTokenExchange($addTokenExchange);

                // quick and dirty position adding
                if ('t' === $data['active']) {
                    $history = $this->getStorageHistory
                        ->getStorageHistory(
                            Contract::fromString($data['address_quipuswap']),
                            Decimals::fromInt($metadata['decimals'])
                        )
                        ->history();
                    $history                          = end($history);
                    $this->tezPools[$data['address']] = self::asInt(
                        $history,
                        'tez_pool'
                    );
                }
            }

            fclose($handle);

            arsort($this->tezPools);
            $this->tezPools = array_flip($this->tezPools);
            $position       = 0;
            foreach ($this->tezPools as $address) {
                $token       = $this->application->getOneTokenByAddress($address);
                $updateToken = new UpdateToken(
                    $token->tokenId()->asString(),
                    $token->address()->asString(),
                    $token->metadata(),
                    $token->isActive(),
                    ++$position
                );
                $this->application->updateToken($updateToken);
            }
        }

        return static::SUCCESS;
    }
}
