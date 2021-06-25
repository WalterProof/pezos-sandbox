<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\UX;

use PezosSandbox\Application\Tokens\Token;
use PezosSandbox\Infrastructure\Tezos\Contract;
use PezosSandbox\Infrastructure\Tezos\Decimals;
use PezosSandbox\Infrastructure\Tezos\StorageHistory\GetStorageHistory;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class TokenChart
{
    private SessionInterface $session;
    private GetStorageHistory $getStorageHistory;
    private ChartBuilderInterface $chartBuilder;
    private array $lastUpdates = [];

    public function __construct(
        SessionInterface $session,
        GetStorageHistory $getStorageHistory,
        ChartBuilderInterface $chartBuilder
    ) {
        $this->session           = $session;
        $this->getStorageHistory = $getStorageHistory;
        $this->chartBuilder      = $chartBuilder;
    }

    public function lastUpdates(): array
    {
        return $this->lastUpdates;
    }

    public function createCharts(
        Token $token,
        \DateTimeImmutable $currentTime,
        ?string $interval
    ) {
        $charts   = [];
        $decimals = Decimals::fromInt($token->metadata()['decimals']);

        foreach ($token->exchanges() as $exchange) {
            $contract = Contract::fromString($exchange->contract());
            $history  = $this->getStorageHistory->getStorageHistory(
                $contract,
                $decimals
            );

            $data = $history->history($currentTime, $interval);
            // if it's empty, probably no price action and short interval
            if (empty($data)) {
                $data = $history->history($currentTime, '-1 week');
                $this->session->set('time_interval', '-1 week');
            }

            $charts[$exchange->name()] = [
                'Prices Dynamics' => $this->createPriceChart($data),
                'Pool Dynamics'   => $this->createPoolChart($token, $data),
            ];
            $dates                                = array_keys($data);
            $this->lastUpdates[$exchange->name()] = end($dates);
        }

        return $charts;
    }

    private function createPriceChart(array $data): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $ratios = array_values(array_map(fn (array $d) => $d['ratio'], $data));

        $chart->setData([
            'labels'   => array_keys($data),
            'datasets' => [
                [
                    'borderColor' => 'rgb(51, 51, 51)',
                    'borderWidth' => 1,
                    'data'        => $ratios,
                    'pointRadius' => 0,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'yAxes' => [['ticks' => ['min' => 0, 'max' => max($ratios)]]],
            ],
            'legend' => ['display' => false],
        ]);

        return $chart;
    }

    private function createPoolChart(Token $token, array $data): Chart
    {
        $chart   = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $tezPool = array_values(
            array_map(fn (array $d) => $d['tez_pool'], $data)
        );
        $tokenPool = array_values(
            array_map(fn (array $d) => $d['token_pool'], $data)
        );
        $labels = array_keys($data);

        $chart->setData([
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'       => 'tez',
                    'fill'        => false,
                    'borderColor' => 'rgb(0, 151, 0)',
                    'borderWidth' => 1,
                    'data'        => $tezPool,
                    'pointRadius' => 0,
                    'yAxisID'     => 'tez',
                ],
                [
                    'label'       => $token->metadata()['symbol'],
                    'fill'        => false,
                    'borderColor' => 'rgb(51, 51, 51)',
                    'borderWidth' => 1,
                    'data'        => $tokenPool,
                    'pointRadius' => 0,
                    'yAxisID'     => 'token',
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'yAxes' => [
                    [
                        'id'       => 'tez',
                        'position' => 'left',
                        'ticks'    => [
                            'min' => min($tezPool),
                            'max' => max($tezPool),
                        ],
                    ],
                    [
                        'id'       => 'token',
                        'position' => 'right',
                        'ticks'    => [
                            'min' => min($tokenPool),
                            'max' => max($tokenPool),
                        ],
                    ],
                ],
            ],
        ]);

        return $chart;
    }
}
