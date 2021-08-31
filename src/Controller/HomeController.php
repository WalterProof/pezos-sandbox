<?php

namespace App\Controller;

use App\Repository\TokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private TokenRepository $tokenRepository;

    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $tokens = $this->tokenRepository->findBy(['active' => true]);

        return $this->render('homepage.html.twig', [
            'tokens' => $tokens,
        ]);
    }
}
