<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\UX;

use PezosSandbox\Application\Tokens\Token;
use PezosSandbox\Infrastructure\Tezos\Contract;
use PezosSandbox\Infrastructure\Tezos\Decimals;
use PezosSandbox\Infrastructure\Tezos\StorageHistory\GetStorageHistory;
use PezosSandbox\Infrastructure\Tezos\StorageHistory\StorageHistory;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class TokenChart
{
    private GetStorageHistory $getStorageHistory;
    private ChartBuilderInterface $chartBuilder;
    private array $lastUpdates = [];

    public function __construct(
        GetStorageHistory $getStorageHistory,
        ChartBuilderInterface $chartBuilder
    ) {
        $this->getStorageHistory = $getStorageHistory;
        $this->chartBuilder      = $chartBuilder;
    }

    public function lastUpdates(): array
    {
        return $this->lastUpdates;
    }

    public function createCharts(Token $token)
    {
        $charts   = [];
        $decimals = Decimals::fromInt($token->metadata()['decimals']);

        foreach ($token->exchanges() as $exchange) {
            $contract = Contract::fromString($exchange->contract());
            $history  = $this->getStorageHistory->getStorageHistory(
                $contract,
                $decimals
            );

            $charts[$exchange->name()] = [
                'Prices Dynamics' => $this->createPriceChart($history),
                'Pool Dynamics'   => $this->createPoolChart($history, $token),
            ];
            $dates                                = array_keys($history->history());
            $this->lastUpdates[$exchange->name()] = end($dates);
        }

        return $charts;
    }

    private function createPriceChart(StorageHistory $history): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $ratios = array_values(
            array_map(fn (array $d) => $d['ratio'], $history->history())
        );
        $chart->setData([
            'labels'   => array_keys($history->history()),
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

    private function createPoolChart(
        StorageHistory $history,
        Token $token
    ): Chart {
        $chart   = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $tezPool = array_values(
            array_map(fn (array $d) => $d['tez_pool'], $history->history())
        );
        $tokenPool = array_values(
            array_map(fn (array $d) => $d['token_pool'], $history->history())
        );
        $labels = array_keys($history->history());

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
