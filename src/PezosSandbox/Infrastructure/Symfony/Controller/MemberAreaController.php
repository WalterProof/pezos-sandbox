<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller;

use Bzzhh\Pezos\Keys\Ed25519;
use Bzzhh\Pezos\Keys\PubKey;
use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\FlashType;
use PezosSandbox\Application\Signup\Signup;
use PezosSandbox\Infrastructure\Symfony\Form\LoginForm;
use PezosSandbox\Infrastructure\Symfony\Form\SignupForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MemberAreaController extends AbstractController
{
    private ApplicationInterface $application;
    private UserPasswordEncoderInterface $passwordEncoder;
    private SessionInterface $session;
    private TranslatorInterface $translator;

    public function __construct(
        ApplicationInterface $application,
        UserPasswordEncoderInterface $passwordEncoder,
        SessionInterface $session,
        TranslatorInterface $translator
    ) {
        $this->application     = $application;
        $this->passwordEncoder = $passwordEncoder;
        $this->session         = $session;
        $this->translator      = $translator;
    }

    /**
     * @Route("/membership", name="app_membership", methods={"GET", "POST"})
     */
    public function membership(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $loginForm  = $this->createForm(LoginForm::class);
        $signupForm = $this->createForm(SignupForm::class);
        $signupForm->handleRequest($request);

        if ($signupForm->isSubmitted() && $signupForm->isValid()) {
            $formData = $signupForm->getData();
            $pubKey   = PubKey::fromBase58($formData['pubKey'], new Ed25519());

            try {
                $validSign = $pubKey->verifySignedHex(
                    $formData['signature'],
                    bin2hex($formData['password']),
                );

                if (!$validSign) {
                    $signupForm
                        ->get('signature')
                        ->addError(new FormError('Invalid signature'));
                }
            } catch (\Throwable $t) {
                $signupForm
                    ->get('signature')
                    ->addError(new FormError($t->getMessage()));
            }

            if (isset($validSign) && $validSign) {
                $this->application->signup(
                    new Signup($pubKey->getAddress(), $formData['password']),
                    $this->passwordEncoder,
                );

                $this->session
                    ->getFlashBag()
                    ->add(
                        FlashType::SUCCESS,
                        $this->translator->trans(
                            'signup_success.flash_message',
                        ),
                    );
            }
        }

        return $this->render('member_area/membership.html.twig', [
            'loginForm'  => $loginForm->createView(),
            'signupForm' => $signupForm->createView(),
        ]);
    }
}
