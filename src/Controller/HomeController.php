<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\TezTools\Client as TezTools;
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
    public function index(Request $request): Response
    {
        $identifier = $request->query->get('identifier', self::DEFAULT_TOKEN_IDENTIFIER);

        $tokens        = $this->teztools->fetchContracts();
        $filtered      = array_filter($tokens, fn (Contract $contract): bool => $contract->identifier === $identifier);
        $selectedToken = array_pop($filtered);

        return $this->render('homepage.html.twig', [
            'tokens'        => $tokens,
            'selectedToken' => $selectedToken,
        ]);
    }
}
