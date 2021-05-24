<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Security;

use Bzzhh\Pezos\Keys\Ed25519;
use Bzzhh\Pezos\Keys\PubKey;
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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private ApplicationInterface $application;
    private SessionInterface $session;
    private HttpUtils $httpUtils;
    private TranslatorInterface $translator;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;

    public function __construct(
        ApplicationInterface $application,
        SessionInterface $session,
        HttpUtils $httpUtils,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->application      = $application;
        $this->session          = $session;
        $this->httpUtils        = $httpUtils;
        $this->translator       = $translator;
        $this->urlGenerator     = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder  = $passwordEncoder;
    }

    public function supports(Request $request): bool
    {
        return 'app_login' === $request->attributes->get('_route') &&
            $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        return [
            'msg'        => $request->request->get('msg'),
            'sig'        => $request->request->get('sig'),
            'pubKey'     => $request->request->get('pubKey'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
    }

    public function getUser(
        $credentials,
        UserProviderInterface $userProvider
    ): ?UserInterface {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        try {
            $user = $this->application->getOneMemberByPubKey($credentials['pubKey']);
        } catch (CouldNotFindMember $exception) {
            $this->application->requestAccess(new RequestAccess($credentials['pubKey']));
            $user = $this->application->getOneMemberByPubKey($credentials['pubKey']);
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        $pubKey = PubKey::fromBase58($credentials['pubKey'], new Ed25519());

        return $pubKey->verifySignedHex($credentials['sig'], bin2hex($credentials['msg']));
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
                        'authentication_failed.flash_message',
                    ),
                );
        }

        return $this->redirectToRoute($request, 'index');
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        $providerKey
    ): ?Response {
        if (
            $targetPath = $this->getTargetPath(
                $request->getSession(),
                $providerKey,
            )
        ) {
            return new RedirectResponse($targetPath);
        }

        return $this->httpUtils->createRedirectResponse($request, 'index');
    }

    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate('index');
    }

    private function redirectToRoute(Request $request, string $route): Response
    {
        return $this->httpUtils->createRedirectResponse($request, $route);
    }
}
