<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Security;

use Assert\Assert;
use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\FlashType;
use PezosSandbox\Domain\Model\Member\CouldNotFindMember;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccessTokenAuthenticator extends AbstractGuardAuthenticator
{
    private ApplicationInterface $application;

    private SessionInterface $session;

    private TranslatorInterface $translator;

    private HttpUtils $httpUtils;

    public function __construct(
        ApplicationInterface $application,
        SessionInterface $session,
        TranslatorInterface $translator,
        HttpUtils $httpUtils
    ) {
        $this->application = $application;
        $this->session     = $session;
        $this->translator  = $translator;
        $this->httpUtils   = $httpUtils;
    }

    public function supports(Request $request): bool
    {
        return 'login' === $request->attributes->get('_route') &&
            $request->query->has('token');
    }

    public function getCredentials(Request $request)
    {
        return $request->query->get('token');
    }

    public function getUser(
        $credentials,
        UserProviderInterface $userProvider
    ): ?UserInterface {
        if (null === $credentials) {
            return null;
        }

        Assert::that($credentials)->string(
            'Expected the access token to be a string',
        );

        try {
            return $this->application->getOneMemberByAccessToken($credentials);
        } catch (CouldNotFindMember $exception) {
            return null;
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // If a user was loaded by its access token, we don't need to check anything else
        return true;
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
                        'access_token_authentication_failed.flash_message',
                    ),
                );
        }

        return $this->redirectToRoute($request, 'index');
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $providerKey
    ): ?Response {
        $memberId = $token->getUser()->getUsername();

        return $this->redirectToRoute($request, 'member_area_index');
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    public function start(
        Request $request,
        AuthenticationException $authException = null
    ): Response {
        return $this->redirectToRoute($request, 'index');
    }

    private function redirectToRoute(Request $request, string $route): Response
    {
        return $this->httpUtils->createRedirectResponse($request, $route);
    }
}
