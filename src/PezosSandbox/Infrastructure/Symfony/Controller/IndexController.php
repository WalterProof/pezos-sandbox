<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller;

use PezosSandbox\Application\ApplicationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class IndexController extends AbstractController
{
    private ApplicationInterface $application;

    private TranslatorInterface $translator;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function indexAction(): Response
    {
        return $this->render('index.html.twig', []);
    }
}
