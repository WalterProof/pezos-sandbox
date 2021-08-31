<?php

namespace App\Command;

use App\Entity\Exchange;
use App\Entity\Token;
use App\Entity\TokenExchange;
use App\Mapping;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import',
    description: 'Import data from previous db',
)]
class ImportCommand extends Command
{
    use Mapping;

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        $quip = (new Exchange())
            ->setName('QuipuSwap')
            ->setHomepage('https://quipuswap.com/swap');
        $this->em->persist($quip);

        $tokens = [];
        if (
            ($handle = fopen(
                dirname(__FILE__, 3) . '/data/tokens.csv',
                'r'
            )) !== false
        ) {
            $skip = true;
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
               if($skip) { $skip = false; continue; }
                $tokens[self::asString($data, 0)] = [
                    'address' => self::asString($data, 1),
                    'tokenId' => $data[2],
                    'metadata' => $data[3],
                    'active' => $data[4],
                    'position' => $data[5],
                ];
            }
            fclose($handle);
        }

        if (
            ($handle = fopen(
                dirname(__FILE__, 3) . '/data/token_exchanges.csv',
                'r'
            )) !== false
        ) {
            $skip = true;
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if($skip) { $skip = false; continue; }
                if(isset($tokens[$data[0]])) {
                    $tokens[$data[0]]['quip'] = $data[2];
                }
            }
            fclose($handle);
        }

        foreach ($tokens as $data) {
            $token = (new Token())
                ->setAddress(self::asString($data, 'address'))
                ->setTokenId(self::asIntOrNull($data, 'tokenId'))
                ->setMetadata(self::asArray($data, 'metadata'))
                ->setActive(self::asBool($data, 'active'))
                ->setPosition(self::asIntOrNull($data, 'position'));
            $this->em->persist($token);

            $tokenExchange = (new TokenExchange)->setAddress(self::asString($data, 'quip'))->setExchange($quip)->setToken($token);
            $this->em->persist($tokenExchange);
        }

        $this->em->flush();

        return Command::SUCCESS;
    }
}
