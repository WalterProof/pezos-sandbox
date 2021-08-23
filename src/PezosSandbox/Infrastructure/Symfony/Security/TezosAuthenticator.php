<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Security;

use Bzzhh\Pezos\Keys\PubKey;
use Exception;
use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\FlashType;
use PezosSandbox\Application\RequestAccess\RequestAccess;
use PezosSandbox\Domain\Model\Member\CouldNotFindMember;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

class TezosAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    private ApplicationInterface $application;
    private SessionInterface $session;
    private HttpUtils $httpUtils;
    private TranslatorInterface $translator;
    private $urlGenerator;
    private $csrfTokenManager;

    public function __construct(
        ApplicationInterface $application,
        SessionInterface $session,
        HttpUtils $httpUtils,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->application      = $application;
        $this->session          = $session;
        $this->httpUtils        = $httpUtils;
        $this->translator       = $translator;
        $this->urlGenerator     = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function supports(Request $request): bool
    {
        return 'app_login' === $request->attributes->get('_route') &&
            $request->isMethod('POST');
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): ?Response {
        if ($this->session instanceof Session) {
            $this->session
                ->getFlashBag()
                ->add(
                    FlashType::WARNING,
                    $this->translator->trans(
                        'authentication_failed.flash_message'
                    )
                );
        }

        return $this->redirectToRoute($request, 'app_homepage');
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        $providerKey
    ): ?Response {
        if (
            $targetPath = $this->getTargetPath(
                $request->getSession(),
                $providerKey
            )
        ) {
            return new RedirectResponse($targetPath);
        }

        return $this->httpUtils->createRedirectResponse(
            $request,
            'app_homepage'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(Request $request): PassportInterface
    {
        $message   = $request->request->get('msg');
        $signature = $request->request->get('sig');
        $pubKey    = $request->request->get('pubKey');
        $csrfToken = $request->request->get('_csrf_token');

        $pubKey = PubKey::fromBase58($pubKey);
        if (
            !$pubKey->verifySignature($signature, $message) &&
            !$pubKey->verifySignature($signature, bin2hex($message))
        ) {
            throw new Exception('Signature verification failed');
        }

        try {
            $this->application->getOneMemberByPubKey($pubKey->getPublicKey());
        } catch (CouldNotFindMember $exception) {
            $this->application->requestAccess(
                new RequestAccess(
                    $pubKey->getPublicKey(),
                    $pubKey->getAddress()
                )
            );
        }

        return new SelfValidatingPassport(
            new UserBadge($pubKey->getPublicKey(), function ($pubKey) {
                return $this->application->getOneMemberByPubKey($pubKey);
            }),
            [new CsrfTokenBadge('authenticate', $csrfToken)]
        );
    }

    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate('app_homepage');
    }

    private function redirectToRoute(Request $request, string $route): Response
    {
        return $this->httpUtils->createRedirectResponse($request, $route);
    }
}
