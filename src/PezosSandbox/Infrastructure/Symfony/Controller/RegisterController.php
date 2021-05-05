<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller;

use PezosSandbox\Application\ApplicationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PezosSandbox\Infrastructure\Symfony\Form\SignupForm;

final class RegisterController extends AbstractController
{
    private ApplicationInterface $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
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
