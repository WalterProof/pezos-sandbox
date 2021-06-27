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
        $unit     = $interval && strpos($interval, 'hours') ? 'hour' : 'day';
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
                'Prices Dynamics' => $this->createPriceChart($data, $unit),
                'Pool Dynamics'   => $this->createPoolChart($token, $data, $unit),
            ];
            $dates                                = array_keys($data);
            $this->lastUpdates[$exchange->name()] = end($dates);
        }

        return $charts;
    }

    private function createPriceChart(array $data, string $unit): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $ratios = array_values(array_map(fn (array $d) => $d['ratio'], $data));

        $chart->setData([
            'labels'   => array_keys($data),
            'datasets' => [
                [
                    'borderColor'     => 'rgb(59,130,246)',
                    'backgroundColor' => 'rgb(59,130,246)',
                    'borderWidth'     => 2,
                    'data'            => $ratios,
                    'radius'          => 0,
                    'fill'            => false,
                    'tension'         => 0,
                ],
            ],
        ]);

        $chart->setOptions([
            'animation' => false,
            'scales'    => [
                'yAxes' => [
                    ['ticks' => ['min' => min($ratios), 'max' => max($ratios)]],
                ],
                'xAxes' => [
                    [
                        'type' => 'time',
                        'time' => [
                            'unit' => $unit,
                        ],
                    ],
                ],
            ],
            'legend'   => ['display' => false],
            'tooltips' => ['intersect' => false],
        ]);

        return $chart;
    }

    private function createPoolChart(
        Token $token,
        array $data,
        string $unit
    ): Chart {
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
                    'label'           => 'tez',
                    'fill'            => false,
                    'borderColor'     => 'rgb(59,130,246)',
                    'backgroundColor' => 'rgb(59,130,246)',
                    'borderWidth'     => 2,
                    'data'            => $tezPool,
                    'radius'          => 0,
                    'yAxisID'         => 'tez',
                    'tension'         => 0,
                ],
                [
                    'label'           => $token->metadata()['symbol'],
                    'fill'            => false,
                    'borderColor'     => 'rgb(245,158,11)',
                    'backgroundColor' => 'rgb(245,158,11)',
                    'borderWidth'     => 2,
                    'data'            => $tokenPool,
                    'radius'          => 0,
                    'yAxisID'         => 'token',
                    'tension'         => 0,
                ],
            ],
        ]);

        $chart->setOptions([
            'animation' => false,
            'tooltips'  => ['intersect' => false, 'mode' => 'index'],
            'scales'    => [
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
                'xAxes' => [
                    [
                        'type' => 'time',
                        'time' => [
                            'unit' => $unit,
                        ],
                    ],
                ],
            ],
        ]);

        return $chart;
    }
}
