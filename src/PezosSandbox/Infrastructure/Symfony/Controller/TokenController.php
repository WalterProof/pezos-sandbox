<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller;

use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Infrastructure\Tezos\Contract;
use PezosSandbox\Infrastructure\Tezos\Decimals;
use PezosSandbox\Infrastructure\Tezos\StorageHistory\GetStorageHistory;
use PezosSandbox\Infrastructure\UX\TokenChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TokenController extends AbstractController
{
    private ApplicationInterface $application;
    private GetStorageHistory $getStorageHistory;
    private TokenChart $tokenChart;

    public function __construct(
        ApplicationInterface $application,
        GetStorageHistory $getStorageHistory,
        TokenChart $tokenChart
    ) {
        $this->application       = $application;
        $this->getStorageHistory = $getStorageHistory;
        $this->tokenChart        = $tokenChart;
    }

    /**
     * @Route("/token/charts/{address}", name="_app_token_charts", methods={"GET"})
     */
    public function charts(Request $request): Response
    {
        $address     = $request->get('address');
        $token       = $this->application->getOneTokenByAddress($address);
        $charts      = $this->tokenChart->createCharts($token);
        $lastUpdates = $this->tokenChart->lastUpdates();

        return $this->render('_token_charts.html.twig', [
            'token'       => $token,
            'charts'      => $charts,
            'lastUpdates' => $lastUpdates,
        ]);
    }

    /**
     * @Route("/token/last-update/{address}", name="_app_token_last_update", methods={"GET"})
     */
    public function lastUpdate(Request $request): Response
    {
        $address = $request->get('address');
        $token   = $this->application->getOneTokenByAddress($address);

        $history = $this->getStorageHistory
            ->getStorageHistory(
                Contract::fromString($token->exchanges()[0]->contract()),
                Decimals::fromInt($token->metadata()['decimals'])
            )
            ->history();

        return $this->render('_token_last_update.html.twig', [
            'token'           => $token,
            'tokenLastUpdate' => end($history),
        ]);
    }
}
