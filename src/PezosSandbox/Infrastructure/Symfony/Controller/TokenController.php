<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller;

use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Infrastructure\Symfony\Form\TimeIntervalForm;
use PezosSandbox\Infrastructure\Tezos\Contract;
use PezosSandbox\Infrastructure\Tezos\Decimals;
use PezosSandbox\Infrastructure\Tezos\StorageHistory\GetStorageHistory;
use PezosSandbox\Infrastructure\UX\TokenChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

final class TokenController extends AbstractController
{
    private ApplicationInterface $application;
    private GetStorageHistory $getStorageHistory;
    private TokenChart $tokenChart;
    private SessionInterface $session;

    public function __construct(
        ApplicationInterface $application,
        GetStorageHistory $getStorageHistory,
        TokenChart $tokenChart,
        SessionInterface $session
    ) {
        $this->application       = $application;
        $this->getStorageHistory = $getStorageHistory;
        $this->tokenChart        = $tokenChart;
        $this->session           = $session;
    }

    /**
     * @Route("/token/charts/{address}", name="_app_token_charts", methods={"GET"})
     */
    public function charts(Request $request): Response
    {
        $address = $request->get('address');
        $token   = $this->application->getOneTokenByAddress($address);
        $charts  = $this->tokenChart->createCharts(
            $token,
            $this->application->getCurrentTime(),
            $this->session->get('time_interval')
        );
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
        $address  = $request->get('address');
        $token    = $this->application->getOneTokenByAddress($address);
        $exchange = isset($token->exchanges()[0])
            ? $token->exchanges()[0]
            : null;

        $history = $this->getStorageHistory
            ->getStorageHistory(
                Contract::fromString($exchange->contract()),
                Decimals::fromInt($token->metadata()['decimals'])
            )
            ->history($this->application->getCurrentTime());

        $last = \array_slice($history, -1, 1, true);

        return $this->render('_token_last_update.html.twig', [
            'token'           => $token,
            'tokenLastUpdate' => array_merge(
                ['datetime' => current(array_keys($last))],
                current($last)
            ),
        ]);
    }

    /**
     * @Route("/token/diff/{address}", name="_app_token_diff", methods={"GET"})
     */
    public function diff(Request $request): Response
    {
        $address = $request->get('address');
        $token   = $this->application->getOneTokenByAddress($address);

        $diff = $this->getStorageHistory
            ->getStorageHistory(
                Contract::fromString($token->exchanges()[0]->contract()),
                Decimals::fromInt($token->metadata()['decimals'])
            )
            ->diff();

        return new JsonResponse($diff);
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
