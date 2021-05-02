<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller;

use Assert\Assert;
use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\Members\Member;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/member-area")
 */
final class MemberAreaController extends AbstractController
{
    private ApplicationInterface $application;

    private TranslatorInterface $translator;

    public function __construct(
        ApplicationInterface $application,
        TranslatorInterface $translator
    ) {
        $this->application = $application;
        $this->translator  = $translator;
    }

    /**
     * @Route("/", name="member_area_index", methods={"GET"})
     */
    public function indexAction(UserInterface $member): Response
    {
        Assert::that($member)->isInstanceOf(Member::class);
        /* @var Member $member */

        return $this->render('member_area/index.html.twig', []);
    }
}
