<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller;

use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\RequestAccess\RequestAccess;
use PezosSandbox\Domain\Model\Common\UserFacingError;
use PezosSandbox\Domain\Model\Member\CouldNotGrantAccess;
use PezosSandbox\Infrastructure\Symfony\Form\RequestAccessForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    private ApplicationInterface $application;

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
     * @Route("/request-access", name="request_access", methods={"POST"})
     */
    public function requestAccessAction(Request $request): Response
    {
        $json = $this->getJson($request);
        $form = $this->createForm(RequestAccessForm::class);
        $form->submit($json);

        if ($form->isValid()) {
            $formData = $form->getData();

            try {
                $token = $this->application->requestAccess(
                    new RequestAccess(
                        bin2hex($formData['payload']),
                        $formData['publicKey'],
                        $formData['signature'],
                    ),
                );

                return $this->json(['token' => $token]);
            } catch (CouldNotGrantAccess $exception) {
                $this->convertExceptionToFormError(
                    $form,
                    'address',
                    $exception,
                );
            }
        }

        if (\count($form->getErrors() > 0)) {
            return $this->json(
                ['errors' => $form->getErrors()],
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    /**
     * @Route("/login", name="login", methods={"GET"})
     */
    public function loginAction(): Response
    {
        return new RedirectResponse($this->generateUrl('index'));
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logoutAction(): Response
    {
        return new RedirectResponse($this->generateUrl('index'));
    }

    /**
     * @throws HttpException
     */
    private function getJson(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new HttpException(400, 'Invalid json');
        }

        return $data;
    }

    private function convertExceptionToFormError(
        FormInterface $form,
        string $field,
        UserFacingError $exception
    ): void {
        $form
            ->get($field)
            ->addError(
                new FormError(
                    $this->translator->trans(
                        $exception->translationId(),
                        $exception->translationParameters(),
                    ),
                    $exception->translationId(),
                    $exception->translationParameters(),
                ),
            );
    }
}
