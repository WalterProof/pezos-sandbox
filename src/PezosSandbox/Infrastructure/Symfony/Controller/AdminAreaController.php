<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller;

use PezosSandbox\Application\ApplicationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin-area")
 */
final class AdminAreaController extends AbstractController
{
    private ApplicationInterface $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * @Route("/", name="admin_area_index", methods={"GET"})
     */
    public function indexAction(): Response
    {
        return $this->render('admin_area/index.html.twig', [
            'members' => $this->application->listMembersForAdministrator(),
        ]);
    }

    /**
     * @Route("/logout", name="admin_area_logout", methods={"GET"})
     */
    public function logoutAction(): Response
    {
        return new RedirectResponse($this->generateUrl('index'));
    }
}
