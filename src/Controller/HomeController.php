<?php

declare(strict_types=1);

namespace App\Controller;

use App\Builder\ChartBuilder;
use App\Form\TimeIntervalForm;
use App\Http\TezTools\CachedClient;
use App\Http\TezTools\Model\Contract;
use App\Model\Chart;
use App\Repository\PriceHistoryRepository;
use App\System\Clock;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public const DEFAULT_TOKEN_IDENTIFIER = 'KT1GRSvLoikDsXujKgZPsGLX8k8VvR2Tq95b';

    public function __construct(
        private CachedClient $teztools,
        private PriceHistoryRepository $priceHistoryRepository,
        private SessionInterface $session,
        private Clock $clock
    ) {
    }

    #[Route('/', name: 'home')]
    public function index(Request $request, ChartBuilder $chartBuilder): Response
    {
        $identifier = $request->query->get('identifier', self::DEFAULT_TOKEN_IDENTIFIER);
        if (null === $this->session->get('time_interval')) {
            $this->session->set('time_interval', '-24 hours');
        }

        $timeIntervalForm = $this->createForm(TimeIntervalForm::class);
        $tokens           = $this->teztools->fetchContracts();
        $filtered         = array_filter($tokens, fn (Contract $contract): bool => $contract->identifier === $identifier);
        /** @var Contract $selectedToken */
        $selectedToken    = array_pop($filtered);

        $interval = $this->session->get('time_interval');
        $fromDate = 'max' !== $interval ? $this->clock->currentTime()->modify($interval) : null;
        $datePart = match ($interval) {
            '-24 hours', '-7 days', '-14 days', '-30 days' => null,
            '-90 days', '-180 days' => 'minute',
            '180 days', '-1 year', 'max' => 'hour',
        };

        $history = $this->priceHistoryRepository->fromDate($selectedToken->identifier, $datePart, $fromDate);

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
                    ['ticks' => ['min' => min($prices), 'max' => max($prices)]],
                ],
            ],
            'plugins'  => ['legend'   => ['display' => false]],
            'tooltips' => ['intersect' => false],
        ]);

        return $this->render('homepage.html.twig', [
            'tokens'           => $tokens,
            'selectedToken'    => $selectedToken,
            'chart'            => $chart,
            'timeIntervalForm' => $timeIntervalForm->createView(),
        ]);
    }

    /**
     * @Route("/token/time-interval", name="_app_time_interval", methods={"POST"})
     */
    public function timeInterval(Request $request): Response
    {
        $timeIntervalForm = $this->createForm(TimeIntervalForm::class);
        $timeIntervalForm->handleRequest($request);

        if ($timeIntervalForm->isSubmitted() && $timeIntervalForm->isValid()) {
            $formData = $timeIntervalForm->getData();
            $this->session->set('time_interval', $formData['interval']);
        }

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
