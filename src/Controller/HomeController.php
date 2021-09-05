<?php

declare(strict_types=1);

namespace App\Controller;

use App\Builder\ChartBuilder;
use App\Http\TezTools\Client as TezTools;
use App\Model\Chart;
use App\Model\Contract;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public const DEFAULT_TOKEN_IDENTIFIER = 'KT1GRSvLoikDsXujKgZPsGLX8k8VvR2Tq95b';

    public function __construct(
        private TezTools $teztools
    ) {
    }

    #[Route('/', name: 'home')]
    public function index(Request $request, ChartBuilder $chartBuilder): Response
    {
        $identifier = $request->query->get('identifier', self::DEFAULT_TOKEN_IDENTIFIER);

        $tokens        = $this->teztools->fetchContracts();
        $filtered      = array_filter($tokens, fn (Contract $contract): bool => $contract->identifier === $identifier);
        $selectedToken = array_pop($filtered);

        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels'   => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label'           => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor'     => 'rgb(255, 99, 132)',
                    'data'            => [0, 10, 5, 2, 20, 30, 45],
                ],
            ],
        ]);

        /* $chart->setOptions([ */
        /*     'scales' => [ */
        /*         'yAxes' => [ */
        /*             ['ticks' => ['min' => 0, 'max' => 100]], */
        /*         ], */
        /*     ], */
        /* ]); */

        return $this->render('homepage.html.twig', [
            'tokens'        => $tokens,
            'selectedToken' => $selectedToken,
            'chart'         => $chart,
        ]);
    }
}
