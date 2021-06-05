<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Command;

use PezosSandbox\Application\AddToken;
use PezosSandbox\Application\ApplicationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportTokens extends Command
{
    private ApplicationInterface $application;
    private array $headers = [];

    public function __construct(ApplicationInterface $application)
    {
        parent::__construct();

        $this->application  = $application;
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
            ($handle = fopen(\dirname(__DIR__, 5).'/data/tokens.csv', 'r')) !==
            false
        ) {
            while (($data = fgetcsv($handle)) !== false) {
                ++$count;

                if (1 === $count) {
                    $this->headers = $data;
                    continue;
                }

                $data   = array_combine($this->headers, $data);
                $social = [
                    'twitter'   => $data['twitter'],
                    'telegram'  => $data['telegram'],
                    'discord'   => $data['discord'],
                    'github'    => $data['github'],
                    'instagram' => $data['instagram'],
                ];
                $social = array_filter($social, fn (string $url) => !empty($url));

                $addToken = new AddToken(
                    $data['address'],
                    $data['address_quipuswap'],
                    $data['kind'],
                    \intval($data['decimals']),
                    $data['symbol'],
                    $data['name'],
                    $data['description'],
                    $data['homepage'],
                    $social,
                    $data['thumbnail_uri'],
                    't' === $data['active'] ? true : false
                );

                $this->application->addToken($addToken);
            }
            fclose($handle);
        }

        return static::SUCCESS;
    }
}
