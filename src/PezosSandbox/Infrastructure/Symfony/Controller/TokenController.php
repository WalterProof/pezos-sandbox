<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller;

use Bzzhh\Tzkt\Api\ContractsApi;
use PezosSandbox\Application\AddToken;
use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\UpdateToken;
use PezosSandbox\Domain\Model\Token\Token;
use PezosSandbox\Infrastructure\Symfony\Form\AddTokenForm;
use PezosSandbox\Infrastructure\Symfony\Form\EditTokenForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TokenController extends AbstractController
{
    private ApplicationInterface $application;
    private TranslatorInterface $translator;
    private ContractsApi $contractsApi;

    public function __construct(
        ApplicationInterface $application,
        TranslatorInterface $translator,
        ContractsApi $contractsApi
    ) {
        $this->application  = $application;
        $this->translator   = $translator;
        $this->contractsApi = $contractsApi;
    }

    /**
     * @Route("/tokens", name="app_token_list", methods={"GET"})
     */
    public function list(): Response
    {
        $tokens = $this->application->listTokensForAdmin();

        return $this->render('tokens/list.html.twig', [
            'tokens' => $tokens,
        ]);
    }

    /**
     * @Route("/tokens/new", name="app_token_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $tokenForm = $this->createForm(AddTokenForm::class);
        $tokenForm->handleRequest($request);

        if ($tokenForm->isSubmitted() && $tokenForm->isValid()) {
            $formData = $tokenForm->getData();
            $token    = $formData['token'];

            try {
                $addToken = new AddToken(
                    $formData['address'],
                    $token['addressQuipuswap'],
                    $token['kind'],
                    \intval($token['decimals']),
                    $token['supplyAdjustment'],
                    $token['symbol'],
                    $token['name'],
                    $token['description'],
                    $token['homepage'],
                    $token['thumbnailUri'],
                    $token['active'],
                );

                $this->application->addToken($addToken);

                $this->redirectToRoute('app_token_list');
            } catch (\Exception $e) {
                $tokenForm->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('tokens/new.html.twig', [
            'tokenForm' => $tokenForm->createView(),
        ]);
    }

    /**
     * @Route("/tokens/edit/{address}", name="app_token_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request): Response
    {
        $address   = $request->attributes->get('address');
        $token     = $this->application->getOneTokenByAddress($address);
        $tokenForm = $this->createForm(EditTokenForm::class, [
            'address'           => $token->address(),
            'addressQuipuswap'  => $token->addressQuipuswap(),
            'symbol'            => $token->symbol(),
            'kind'              => $token->kind(),
            'name'              => $token->name(),
            'description'       => $token->description(),
            'social'            => json_encode($token->social()),
            'homepage'          => $token->homepage(),
            'thumbnailUri'      => $token->thumbnailUri(),
            'decimals'          => $token->decimals(),
            'supplyAdjustment'  => $token->supplyAdjustment(),
            'active'            => $token->active(),
        ]);
        $tokenForm->handleRequest($request);

        if ($tokenForm->isSubmitted() && $tokenForm->isValid()) {
            $formData = $tokenForm->getData();

            try {
                $updateToken = new UpdateToken(
                    $address,
                    $formData['addressQuipuswap'],
                    $formData['kind'],
                    \intval($formData['decimals']),
                    $formData['supplyAdjustment'],
                    $formData['symbol'],
                    $formData['name'],
                    $formData['description'],
                    $formData['homepage'],
                    json_decode($formData['social'], true),
                    $formData['thumbnailUri'],
                    $formData['active'],
                );

                $this->application->updateToken($updateToken);

                $this->redirectToRoute('app_token_list');
            } catch (\Exception $e) {
                $tokenForm->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('tokens/edit.html.twig', [
            'tokenForm' => $tokenForm->createView(),
            'address'   => $address,
        ]);
    }

    /**
     * @Route("/tokens/toggle/{address}", name="app_token_toggle", methods={"POST"})
     */
    public function toggleActive(Request $request): Response
    {
        $address   = $request->attributes->get('address');
        $token     = $this->application->getOneTokenByAddress($address);
        $this->application->toggleToken();
    }
}
