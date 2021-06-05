<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller;

use Bzzhh\Tzkt\Api\ContractsApi;
use Bzzhh\Tzkt\Model\StorageRecord;
use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\Tokens\Token;
use PezosSandbox\Domain\Model\Token\Token as TokenToken;
use PezosSandbox\Infrastructure\Symfony\Form\LoginForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class IndexController extends AbstractController
{
    private ApplicationInterface $application;
    private AdapterInterface $cache;
    private ContractsApi $apiInstance;

    public function __construct(
        ApplicationInterface $application,
        AdapterInterface $cache,
        ContractsApi $apiInstance
    ) {
        $this->application = $application;
        $this->cache       = $cache;
        $this->apiInstance = $apiInstance;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(
        ChartBuilderInterface $chartBuilder,
        Request $request
    ): Response {
        $loginForm  = $this->createForm(LoginForm::class);

        $tokens       = $this->application->listTokens();
        $tokensByKind = array_reduce(
            $tokens,
            function (array $acc, Token $token) {
                $acc[$token->kind()][] = $token;

                return $acc;
            },
            [],
        );

        $selectedAddress = $request->query->get(
            'address',
            $tokens[0]->address(),
        );
        $selectedToken = $this->application->getOneTokenByAddress(
            $selectedAddress,
        );
        $data  = $this->getPoolData($selectedToken);
        $infos = $this->getSelectedTokenInfos($selectedToken);

        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels'   => array_keys($data),
            'datasets' => [
                [
                    'borderColor' => 'rgb(51, 51, 51)',
                    'borderWidth' => 1,
                    'data'        => array_values($data),
                    'pointRadius' => 0,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'yAxes' => [['ticks' => ['min' => 0, 'max' => max($data)]]],
            ],
            'legend' => ['display' => false],
        ]);

        return $this->render('index.html.twig', [
            'loginForm'     => $loginForm->createView(),
            'tokensByKind'  => $tokensByKind,
            'chart'         => $chart,
            'tokens'        => $tokens,
            'selectedToken' => $selectedToken,
            'infos'         => $infos,
        ]);
    }

    private function getSelectedTokenInfos(Token $token)
    {
        $key    = sprintf('token_infos_%s', $token->symbol());
        $cached = $this->cache->getItem($key);

        if (!$cached->isHit()) {
            $dex = json_decode(
                $this->apiInstance
                    ->contractsGetStorage($token->addressQuipuswap())
                    ->current(),
            );

            $tokenAddress =
                TokenToken::KIND_FA1_2 === $token->kind()
                    ? $token->address()
                    : substr(
                        $token->address(),
                        0,
                        strpos($token->address(), '_'),
                    );

            $totalSupply = $this->guessTotalSupply($tokenAddress);

            $data = [
                'tez_pool'      => $dex->storage->tez_pool / 1_000_000,
                'token_pool'    => $dex->storage->token_pool / 10 ** $token->decimals(),
                'token_address' => $tokenAddress,
                'total_supply'  => $totalSupply
                    ? $totalSupply / 10 ** $token->decimals()
                    : null,
            ];
            $cached
                ->set($data)
                ->expiresAfter(
                    \DateInterval::createFromDateString('60 seconds'),
                );
            $this->cache->save($cached);
        }

        return $cached->get();
    }

    private function guessTotalSupply(string $address): ?string
    {
        $storage = json_decode(
            $this->apiInstance->contractsGetStorage($address)->current(),
        );

        if (isset($storage->totalSupply)) {
            return $storage->totalSupply;
        }

        if (isset($storage->total_supply)) {
            return $storage->total_supply;
        }

        if (isset($storage->token) && isset($storage->token->totalSupply)) {
            return $storage->token->totalSupply;
        }

        if (isset($storage->assets) && isset($storage->assets->total_supply)) {
            return $storage->assets->total_supply;
        }

        return null;
    }

    private function getPoolData(Token $token)
    {
        $cached       = $this->cache->getItem(sprintf('price_dynamics_%s', $token->symbol()));
        $cachedbackup = $this->cache->getItem(sprintf('price_dynamics_backup_%s', $token->symbol()));
        $cachedLastId = $this->cache->getItem(sprintf('last_id_%s', $token->symbol()));
        $lastId       = $cachedLastId->isHit() ? $cachedLastId->get() : 0;

        if (!$cached->isHit()) {
            $limit  = 1000;

            $data = $cachedbackup->isHit() ? array_reverse($cachedbackup->get()) : [];

            do {
                $storage = $this->apiInstance->contractsGetStorageHistory(
                    $token->addressQuipuswap(),
                    $lastId,
                    $limit,
                );

                $data = array_merge(
                    $data,
                    array_reduce(
                        array_map(function (StorageRecord $record) use (
                            $token
                        ) {
                            $tezPool =
                                $record->getValue()['storage']->tez_pool /
                                1_000_000;
                            $tokenPool =
                                $record->getValue()['storage']->token_pool /
                                10 ** $token->decimals();

                            return [
                                $record
                                    ->getTimestamp()
                                    ->format('Y-m-d H:i:s') => $tezPool / $tokenPool,
                            ];
                        },
                        $storage),
                        fn (array $record, array $acc) => array_merge(
                            $record,
                            $acc,
                        ),
                        [],
                    ),
                );

                $lastId = \count($storage) > 0 ? end($storage)->getId() : $lastId;
            } while (\count($storage) === $limit);

            $data = array_reverse($data);

            $cached
                ->set($data)
                ->expiresAfter(
                    \DateInterval::createFromDateString('60 seconds'),
                );
            $cachedbackup->set($data);
            $cachedLastId->set($lastId);

            $this->cache->save($cached);
            $this->cache->save($cachedLastId);
            $this->cache->save($cachedbackup);
        }

        return $cached->get();
    }
}
