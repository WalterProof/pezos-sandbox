<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Command;

use PezosSandbox\Application\AddExchange;
use PezosSandbox\Application\AddToken;
use PezosSandbox\Application\AddTokenExchange;
use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\UpdateToken;
use PezosSandbox\Infrastructure\Mapping;
use function Safe\json_decode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportTokens extends Command
{
    use Mapping;

    private ApplicationInterface $application;
    private array $headers = [];

    public function __construct(ApplicationInterface $application)
    {
        parent::__construct();

        $this->application = $application;
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
            }

            fclose($handle);
        }

        return static::SUCCESS;
    }
}
