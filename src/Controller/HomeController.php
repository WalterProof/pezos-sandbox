<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\TezTools\Client as TezTools;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private TezTools $teztools
    ) {
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $tokens       = $this->teztools->fetchContracts();
        $currentToken = array_pop($tokens);

        return $this->render('homepage.html.twig', [
            'tokens'       => $tokens,
            'currentToken' => $currentToken,
        ]);
    }
}
