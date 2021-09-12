<?php

declare(strict_types=1);

namespace App\Controller;

use App\Builder\ChartBuilder;
use App\Http\TezTools\CachedClient;
use App\Http\TezTools\Model\Contract;
use App\Model\Chart;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public const DEFAULT_TOKEN_IDENTIFIER = 'KT1GRSvLoikDsXujKgZPsGLX8k8VvR2Tq95b';

    public function __construct(
        private CachedClient $teztools
    ) {
    }

    #[Route('/', name: 'home')]
    public function index(Request $request, ChartBuilder $chartBuilder): Response
    {
        $identifier = $request->query->get('identifier', self::DEFAULT_TOKEN_IDENTIFIER);

        $tokens        = $this->teztools->fetchContracts();
        $filtered      = array_filter($tokens, fn (Contract $contract): bool => $contract->identifier === $identifier);
        $selectedToken = array_pop($filtered);

        $interval     = '-1 week';
        $priceHistory = $this->teztools->fetchPriceHistory($selectedToken->identifier)->byInterval(new DateTimeImmutable(), $interval);
        $prices       = array_values(array_map(fn (array $item) => $item['price'], $priceHistory));

        $chart        = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels'   => array_keys($priceHistory),
            'datasets' => [
                [
                    'borderColor'     => 'rgb(59,130,246)',
                    'backgroundColor' => 'rgb(59,130,246)',
                    'borderWidth'     => 1.5,
                    'data'            => $prices,
                    'radius'          => 0,
                    'fill'            => false,
                    'tension'         => 0,
                ],
            ],
        ]);

        $unit     = $interval && strpos($interval, 'hours') ? 'hour' : 'day';
        $chart->setOptions([
            'animation' => false,
            'scales'    => [
                'yAxes' => [
                    ['ticks' => ['min' => min($prices), 'max' => max($prices)]],
                ],
                'xAxes' => [
                    [
                        'type' => 'time',
                        'time' => [
                            'unit' => $unit,
                        ],
                        'gridLines' => ['display' => false],
                    ],
                ],
            ],
            'legend'   => ['display' => false],
            'tooltips' => ['intersect' => false],
        ]);

        return $this->render('homepage.html.twig', [
            'tokens'        => $tokens,
            'selectedToken' => $selectedToken,
            'chart'         => $chart,
        ]);
    }
}
