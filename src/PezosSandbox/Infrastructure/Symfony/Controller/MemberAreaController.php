<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller;

use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Infrastructure\Symfony\Form\LoginForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PezosSandbox\Infrastructure\Symfony\Form\SignupForm;

final class MemberAreaController extends AbstractController
{
    private ApplicationInterface $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * @Route("/membership", name="app_membership", methods={"GET", "POST"})
     */
    public function membership(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $loginForm = $this->createForm(LoginForm::class);
        $signupForm = $this->createForm(SignupForm::class);
        $signupForm->handleRequest($request);

        if ($signupForm->isSubmitted() && $signupForm->isValid()) {
            $formData = $signupForm->getData();
        }

        return $this->render('member_area/membership.html.twig', [
            'loginForm' => $loginForm->createView(),
            'signupForm' => $signupForm->createView(),
        ]);
    }

    /**
     * @Route("/signup", name="app_signup", methods={"GET", "POST"})
     */
    public function signup(Request $request): Response
    {
        $form = $this->createForm(SignupForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            dump($formData);

            /*             try { */
            /*                 $this->application->requestAccess( */
            /*                     new RequestAccess( */
            /*                         $formData['leanpubInvoiceId'], */
            /*                         $formData['emailAddress'], */
            /*                         $formData['timeZone'], */
            /*                     ), */
            /*                 ); */

            /*                 return $this->redirectToRoute('access_requested'); */
            /*             } catch (LeanpubInvoiceIdHasBeenUsedBefore $exception) { */
            /*                 $this->convertExceptionToFormError( */
            /*                     $form, */
            /*                     'leanpubInvoiceId', */
            /*                     $exception, */
            /*                 ); */
            /*             } */
        }

        return $this->render('register/signup.html.twig', [
            'signupForm' => $this->createForm(SignupForm::class)->createView(),
        ]);
    }
}
