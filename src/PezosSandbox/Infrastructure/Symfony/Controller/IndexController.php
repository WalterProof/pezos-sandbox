<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller;

use PezosSandbox\Application\ApplicationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/request-access-token", name="request_access_token", methods={"POST"})
     */
    public function requestAccessTokenAction(Request $request): Response
    {
        $form = $this->createForm(RequestAccessTokenForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            try {
                $this->application->generateAccessToken(
                    $formData['leanpubInvoiceId'],
                );

                return $this->redirectToRoute('index');
            } catch (CouldNotFindMember $exception) {
                $this->convertExceptionToFormError(
                    $form,
                    'leanpubInvoiceId',
                    $exception,
                );
            } catch (CouldNotGenerateAccessToken $exception) {
                $this->convertExceptionToFormError(
                    $form,
                    'leanpubInvoiceId',
                    $exception,
                );
            }
        }

        return $this->render('index.html.twig', [
            'requestAccessTokenForm' => $form->createView(),
            'requestAccessForm'      => $this->createForm(
                RequestAccessForm::class,
            )->createView(),
        ]);
    }
}
