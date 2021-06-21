<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller;

use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\Tokens\Token;
use PezosSandbox\Infrastructure\Symfony\Form\LoginForm;
use PezosSandbox\Infrastructure\Tezos\Contract;
use PezosSandbox\Infrastructure\Tezos\Decimals;
use PezosSandbox\Infrastructure\Tezos\Storage\GetStorage;
use PezosSandbox\Infrastructure\Tezos\StorageHistory\GetStorageHistory;
use PezosSandbox\Infrastructure\UX\TokenChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    private ApplicationInterface $application;
    private GetStorageHistory $getStorageHistory;
    private $getStorage;
    private TokenChart $tokenChart;

    public function __construct(
        ApplicationInterface $application,
        GetStorageHistory $getStorageHistory,
        GetStorage $getStorage,
        TokenChart $tokenChart
    ) {
        $this->application       = $application;
        $this->getStorageHistory = $getStorageHistory;
        $this->getStorage        = $getStorage;
        $this->tokenChart        = $tokenChart;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $loginForm = $this->createForm(LoginForm::class);
        $tokens    = $this->application->listTokens();

        $address = $request->query->get(
            'address',
            $tokens[0]->address()->asString()
        );
        $token       = $this->application->getOneTokenByAddress($address);
        $charts      = $this->tokenChart->createCharts($token);
        $lastUpdates = $this->tokenChart->lastUpdates();
        $supply      = isset($token->metadata()['supply'])
            ? $token->metadata()['supply']
            : $this->getStorage
                ->getStorage(
                    Contract::fromString($token->address()->contract())
                )
                ->totalSupply();

        return $this->render('index.html.twig', [
            'charts'          => $charts,
            'counters'        => $this->getCounters($tokens),
            'lastUpdates'     => $lastUpdates,
            'loginForm'       => $loginForm->createView(),
            'tokens'          => $tokens,
            'token'           => $token,
            'tokenLastUpdate' => $this->getTokenLastUpdate($token),
            'supply'          => $supply / 10 ** $token->metadata()['decimals'],
        ]);
    }

    private function getTokenLastUpdate(Token $token): array
    {
        $history = $this->getStorageHistory
            ->getStorageHistory(
                Contract::fromString($token->exchanges()[0]->contract()),
                Decimals::fromInt($token->metadata()['decimals'])
            )
            ->history();

        return end($history);
    }

    private function getCounters(array $tokens): array
    {
        return [
            'FA1.2' => array_reduce(
                $tokens,
                fn (int $count, Token $token): int => !$token->address()->id()
                    ? $count + 1
                    : $count,
                0
            ),
            'FA2' => array_reduce(
                $tokens,
                fn (int $count, Token $token): int => $token->address()->id()
                    ? $count + 1
                    : $count,
                0
            ),
        ];
    }
}
