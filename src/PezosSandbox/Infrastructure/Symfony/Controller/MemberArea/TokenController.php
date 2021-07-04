<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller\MemberArea;

use PezosSandbox\Application\AddToken;
use PezosSandbox\Application\AddTokenExchange;
use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\FlashType;
use PezosSandbox\Application\RemoveTokenExchange;
use PezosSandbox\Application\Tokens\TokenExchange;
use PezosSandbox\Application\UpdateToken;
use PezosSandbox\Application\UpdateTokenExchange;
use PezosSandbox\Domain\Model\Common\UserFacingError;
use PezosSandbox\Domain\Model\Token\CouldNotFindToken;
use PezosSandbox\Infrastructure\CacheReset;
use PezosSandbox\Infrastructure\Mapping;
use PezosSandbox\Infrastructure\Symfony\Form\TokenForm;
use PezosSandbox\Infrastructure\Tezos\Contract;
use PezosSandbox\Infrastructure\Tezos\Decimals;
use PezosSandbox\Infrastructure\Tezos\StorageHistory\GetStorageHistory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/member-area")
 */
final class TokenController extends AbstractController
{
    use Mapping;

    private ApplicationInterface $application;
    private TranslatorInterface $translator;
    private GetStorageHistory $getStorageHistory;
    private CacheReset $cacheReset;

    public function __construct(
        ApplicationInterface $application,
        TranslatorInterface $translator,
        GetStorageHistory $getStorageHistory,
        CacheReset $cacheReset
    ) {
        $this->application       = $application;
        $this->translator        = $translator;
        $this->getStorageHistory = $getStorageHistory;
        $this->cacheReset        = $cacheReset;
    }

    /**
     * @Route("/tokens", name="app_token_list", methods={"GET"})
     */
    public function list(): Response
    {
        $tokens = $this->application->listTokensForAdmin();

        return $this->render('member_area/tokens/list.html.twig', [
            'tokens' => $tokens,
        ]);
    }

    /**
     * @Route("/tokens/new", name="app_token_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(TokenForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            try {
                $addToken = new AddToken(
                    $formData['contract'].
                        (null !== $formData['id'] ? '_'.$formData['id'] : ''),
                    $formData['metadata'],
                    $formData['active'],
                    // TODO: form data transformer
                    array_reduce(
                        array_map(
                            fn (array $item): array => [
                                $item['exchangeId'] => $item['contract'],
                            ],
                            $formData['exchanges']
                        ),
                        fn ($acc, $item) => $acc + $item,
                        []
                    )
                );

                $this->application->addToken($addToken);
                $this->addFlash(FlashType::SUCCESS, 'Token added!');

                return $this->redirectToRoute('app_token_list');
            } catch (\Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->renderForm('member_area/tokens/token.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/tokens/edit/{address}", name="app_token_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request): Response
    {
        $address = $request->attributes->get('address');
        try {
            $token = $this->application->getOneTokenByAddress($address);
        } catch (CouldNotFindToken $exception) {
            $this->convertToFlashMessage($exception);

            return $this->redirectToRoute('app_token_list');
        }
        $form = $this->createForm(TokenForm::class, [
            'contract'                              => $token->address()->contract(),
            'id'                                    => $token->address()->id(),
            'metadata'                              => $token->metadata(),
            'active'                                => $token->isActive(),
            'exchanges'                             => array_map(
                fn (TokenExchange $exchange): array => [
                    'exchangeId' => $exchange->exchangeId(),
                    'contract'   => $exchange->contract(),
                ],
                $token->exchanges()
            ),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            try {
                $updateToken = new UpdateToken(
                    $token->tokenId()->asString(),
                    $formData['contract'].
                        (null !== $formData['id'] ? '_'.$formData['id'] : ''),
                    $formData['metadata'],
                    $formData['active'],
                    $token->position()
                );

                $this->application->updateToken($updateToken);

                $currentExchanges = array_reduce(
                    array_map(
                        fn (TokenExchange $item): array => [
                            $item->exchangeId() => $item->contract(),
                        ],
                        $token->exchanges()
                    ),
                    fn ($acc, $item) => $acc + $item,
                    []
                );

                $exchanges = array_reduce(
                    array_map(
                        fn (array $item): array => [
                            $item['exchangeId'] => $item['contract'],
                        ],
                        $formData['exchanges']
                    ),
                    fn ($acc, $item) => $acc + $item,
                    []
                );

                if (\count($formData['exchanges']) > \count($exchanges)) {
                    throw new \Exception('You can only add one contract for each exchange');
                }

                foreach ($token->exchanges() as $tokenExchange) {
                    /** @var TokenExchange $tokenExchange * */
                    if (!isset($exchanges[$tokenExchange->exchangeId()])) {
                        $removeTokenExchange = new RemoveTokenExchange(
                            $token->tokenId()->asString(),
                            $tokenExchange->exchangeId()
                        );
                        $this->application->removeTokenExchange(
                            $removeTokenExchange
                        );
                    }
                }

                foreach ($exchanges as $exchangeId => $contract) {
                    if (!isset($currentExchanges[$exchangeId])) {
                        $addTokenExchange = new AddTokenExchange(
                            $token->tokenId()->asString(),
                            $exchangeId,
                            $contract
                        );
                        $this->application->addTokenExchange($addTokenExchange);
                    } else {
                        $updateTokenExchange = new UpdateTokenExchange(
                            $token->tokenId()->asString(),
                            $exchangeId,
                            $contract
                        );
                        $this->application->updateTokenExchange(
                            $updateTokenExchange
                        );
                    }
                }

                $this->addFlash(FlashType::SUCCESS, 'Token edited!');

                return $this->redirectToRoute('app_token_list');
            } catch (\Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->renderForm('member_area/tokens/token.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/tokens/toggle/{address}", name="app_token_toggle", methods={"POST"})
     */
    public function toggleActive(Request $request): Response
    {
        $address     = $request->attributes->get('address');
        $token       = $this->application->getOneTokenByAddress($address);
        $updateToken = new UpdateToken(
            $token->tokenId()->asString(),
            $token->address()->asString(),
            $token->metadata(),
            !$token->isActive(),
            $token->position(),
            $token->exchanges()
        );

        $this->application->updateToken($updateToken);

        return $this->redirectToRoute('app_token_list');
    }

    /**
     * @Route("/tokens/reorder", name="app_token_reorder", methods={"POST"})
     */
    public function reorder(Request $request): Response
    {
        $tokens   = $this->application->listTokens();
        $tezPools = [];

        foreach ($tokens as $t) {
            $token = $this->application->getOneTokenByAddress(
                $t->address()->asString()
            );
            $tezPools[$token->address()->asString()] = 0;
            if ($token->isActive()) {
                $history = $this->getStorageHistory
                    ->getStorageHistory(
                        Contract::fromString(
                            $token->exchanges()[0]->contract()
                        ),
                        Decimals::fromInt($token->metadata()['decimals'])
                    )
                    ->history($this->application->getCurrentTime());
                $history                                 = end($history);
                $tezPools[$token->address()->asString()] = self::asInt(
                    $history,
                    'tez_pool'
                );
            }
        }

        arsort($tezPools);
        $tezPools = array_flip($tezPools);
        $position = 0;
        foreach ($tezPools as $address) {
            $position    = $position + 1;
            $token       = $this->application->getOneTokenByAddress($address);
            $updateToken = new UpdateToken(
                $token->tokenId()->asString(),
                $token->address()->asString(),
                $token->metadata(),
                $token->isActive(),
                $position,
                $token->exchanges()
            );
            $this->application->updateToken($updateToken);
        }

        return $this->redirectToRoute('app_token_list');
    }

    /**
     * @Route("/tokens/reset-cache/{address}", name="app_token_reset_cache", methods={"POST"})
     */
    public function resetCache(Request $request): Response
    {
        $address = $request->attributes->get('address');
        $token   = $this->application->getOneTokenByAddress($address);

        $keys = [];
        foreach ($token->exchanges() as $exchange) {
            $keys[] = array_merge($keys, [
                $exchange->contract()->asString(),
                sprintf('%s_backup', $exchange->contract()->asString()),
            ]);
        }

        if (\count($keys) !== $this->cacheReset->reset($keys)) {
            $this->addFlash(
                FlashType::WARNING,
                'Cache reset failed! Some cache might not have been correctly deleted, please check.'
            );
        }

        $this->addFlash(
            FlashType::SUCCESS,
            sprintf('Cache reset for %s!', $token->metadata()['symbol'])
        );

        return $this->redirectToRoute('app_token_list');
    }

    private function convertToFlashMessage(UserFacingError $exception): void
    {
        $this->addFlash(
            FlashType::WARNING,
            $this->translator->trans(
                $exception->translationId(),
                $exception->translationParameters()
            )
        );
    }
}
