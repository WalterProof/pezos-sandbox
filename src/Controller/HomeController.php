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
        $identifier = $request->query->get('identifier', self::DEFAULT_TOKEN_IDENTIFIER);
        $session    = $this->requestStack->getSession();
        if (null === $session->get('time_interval')) {
            $session->set('time_interval', '-24 hours');
        }
        if (null === $session->get('chart_kind')) {
            $session->set('chart_kind', 'prices');
        }

        $timeIntervalForm = $this->createForm(TimeIntervalForm::class);
        $chartForm        = $this->createForm(ChartForm::class);
        $contracts        = $this->contractRepository->findAllSelectable();

        $currentContract  = $this->contractRepository->findOneBy(
            ['identifier' => $identifier]
        );

        $interval = $session->get('time_interval');

        $chart = match ($session->get('chart_kind')) {
            'prices'   => $this->getPricesChart($chartBuilder, $identifier, $interval),
              'pools'  => $this->getPoolsChart(
                $chartBuilder,
                $identifier,
                current(array_filter(
                    $contracts,
                    fn (array $token) => $identifier === $token['identifier']
                   ))['symbol'],
                $interval
              )
        };

        return $this->render('homepage.html.twig', [
            'tokens'           => $contracts,
            'selectedToken'    => $currentContract,
            'chart'            => $chart,
            'timeIntervalForm' => $timeIntervalForm->createView(),
            'chartForm'        => $chartForm->createView(),
        ]);
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
                    'grid' => ['display' => false],
                ],
                'y' => [
                    'suggestedMin' => min($prices),
                    'suggestedMax' => max($prices),
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
            'animation' => false,
            'tooltips'  => ['intersect' => false, 'mode' => 'index'],
            'scales'    => [
                'x' => [
                    'type' => 'time',
                    'time' => [
                        'unit' => $unit,
                    ],
                    'gridLines' => ['display' => false],
                ],
                'tez' => [
                    'id'       => 'tez',
                    'position' => 'left',
                    'ticks'    => [
                        'min' => min($tezpool),
                        'max' => max($tezpool),
                    ],
                ],
                'token' => [
                    'id'       => 'token',
                    'position' => 'right',
                    'ticks'    => [
                        'min' => min($tokenpool),
                        'max' => max($tokenpool),
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
