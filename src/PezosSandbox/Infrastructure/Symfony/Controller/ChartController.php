<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller;

use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Infrastructure\UX\TokenChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ChartController extends AbstractController
{
    private ApplicationInterface $application;

    private TokenChart $tokenChart;

    public function __construct(
        ApplicationInterface $application,
        TokenChart $tokenChart
    ) {
        $this->application = $application;
        $this->tokenChart  = $tokenChart;
    }

    /**
     * @Route("/charts/{address}", name="_app_charts", methods={"GET"})
     */
    public function charts(Request $request): Response
    {
        $address     = $request->get('address');
        $token       = $this->application->getOneTokenByAddress($address);
        $charts      = $this->tokenChart->createCharts($token);
        $lastUpdates = $this->tokenChart->lastUpdates();

        return $this->render('_charts.html.twig', [
            'token'       => $token,
            'charts'      => $charts,
            'lastUpdates' => $lastUpdates,
        ]);
    }
}
