<?php

declare(strict_types=1);

namespace App\Controller;

use App\Builder\ChartBuilder;
use App\Form\ChartForm;
use App\Form\TimeIntervalForm;
use App\Http\TezTools\CachedClient;
use App\Model\Chart;
use App\Repository\ContractRepository;
use App\Repository\PriceHistoryRepository;
use App\System\Clock;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public const DEFAULT_TOKEN_IDENTIFIER = 'KT1GRSvLoikDsXujKgZPsGLX8k8VvR2Tq95b';

    public function __construct(
        private CachedClient $teztools,
        private PriceHistoryRepository $priceHistoryRepository,
        private ContractRepository $contractRepository,
        private RequestStack $requestStack,
        private Clock $clock
    ) {
    }

    #[Route('/', name: 'home')]
    public function index(Request $request, ChartBuilder $chartBuilder): Response
    {
        return $this->render('homepage.html.twig', []);
    }

    #[Route('/token/time-interval', name: '_app_time_interval', methods: ['POST'])]
    public function timeInterval(Request $request): Response
    {
        return $this->persistChoiceAndRedirect(
            'time_interval',
            TimeIntervalForm::class,
            $request
        );
    }

    #[Route('/token/chart', name: '_app_chart', methods: ['POST'])]
    public function chart(Request $request): Response
    {
        return $this->persistChoiceAndRedirect(
            'chart_kind',
            ChartForm::class,
            $request
        );
    }

    private function persistChoiceAndRedirect(
        string $key,
        string $formClassName,
        Request $request
    ): Response {
        $session = $this->requestStack->getSession();

        $form = $this->createForm($formClassName);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $session->set($key, $formData[$key]);
        }

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    private function getPricesChart(
        ChartBuilder $chartBuilder,
        string $identifier,
        string $interval,
    ) {
        $fromDate = $this->getFromDate($interval);
        $datePart = $this->getDatePart($interval);
        $history  = $this->priceHistoryRepository->pricesFromDate($identifier, $datePart, $fromDate);

        $prices     = array_column($history, 'price');
        $timestamps = array_column($history, 'timestamp');

        $chart        = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels'   => $timestamps,
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

        $unit = $interval && strpos($interval, 'hours') ? 'hour' : 'day';
        $chart->setOptions([
            'animation'  => false,
            'responsive' => true,
            'scales'     => [
                'x' => [
                    'type' => 'time',
                    'time' => [
                        'unit' => $unit,
                    ],
                    'grid'  => ['display' => false],
                    'ticks' => ['color' => 'lightblue'],
                ],
                'y' => [
                    'ticks'        => ['color' => 'lightblue'],
                ],
            ],
            'plugins'  => [
                'legend'   => ['display' => false],
                'tooltip'  => ['intersect' => false],
            ],
        ]);

        return $chart;
    }

    private function getPoolsChart(
        ChartBuilder $chartBuilder,
        string $identifier,
        string $symbol,
        string $interval,
    ) {
        $fromDate = $this->getFromDate($interval);
        $history  = $this->priceHistoryRepository->poolsFromDate($identifier, $fromDate);

        $timestamps  = array_column($history, 'timestamp');
        $tezpool     = array_column($history, 'tezpool');
        $tokenpool   = array_column($history, 'tokenpool');

        $chart        = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels'   => $timestamps,
            'datasets' => [
                [
                    'label'           => 'tez',
                    'fill'            => false,
                    'borderColor'     => 'rgb(59,130,246)',
                    'backgroundColor' => 'rgb(59,130,246)',
                    'borderWidth'     => 1.5,
                    'data'            => $tezpool,
                    'radius'          => 0,
                    'yAxisID'         => 'tez',
                    'tension'         => 0,
                ],
                [
                    'label'           => $symbol,
                    'color'           => 'lightblue',
                    'fill'            => false,
                    'borderColor'     => 'rgb(245,158,11)',
                    'backgroundColor' => 'rgb(245,158,11)',
                    'borderWidth'     => 1.5,
                    'data'            => $tokenpool,
                    'radius'          => 0,
                    'yAxisID'         => 'token',
                    'tension'         => 0,
                ],
            ],
        ]);

        $unit = $interval && strpos($interval, 'hours') ? 'hour' : 'day';
        $chart->setOptions([
            'animation'  => false,
            'responsive' => true,
            'plugins'    => [
                'legend'   => ['labels' => ['color' => 'lightblue']],
                'tooltip'  => ['intersect' => false],
            ],
            'scales'     => [
                'x' => [
                    'type' => 'time',
                    'time' => [
                        'unit' => $unit,
                    ],
                    'grid'  => ['display' => false],
                    'ticks' => ['color' => 'lightblue'],
                ],
                'tez' => [
                    'id'       => 'tez',
                    'position' => 'left',
                    'ticks'    => [
                        'color' => 'lightblue',
                    ],
                ],
                'token' => [
                    'id'       => 'token',
                    'position' => 'right',
                    'ticks'    => [
                        'color' => 'lightblue',
                    ],
                ],
            ],
        ]);

        return $chart;
    }

    private function getFromDate(string $interval): ?\DateTimeImmutable
    {
        return 'max' !== $interval ? $this->clock->currentTime()->modify($interval) : null;
    }

    private function getDatePart(string $interval): ?string
    {
        return match ($interval) {
            '-24 hours', '-7 days', '-14 days', '-30 days' => null,
            '-90 days', '-180 days' => 'minute',
            '180 days', '-1 year', 'max' => 'hour',
        };
    }
}
