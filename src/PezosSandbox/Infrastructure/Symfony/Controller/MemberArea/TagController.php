<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Controller\MemberArea;

use PezosSandbox\Application\AddTag;
use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\FlashType;
use PezosSandbox\Application\RemoveTag;
use PezosSandbox\Application\UpdateTag;
use PezosSandbox\Domain\Model\Tag\CouldNotFindTag;
use PezosSandbox\Infrastructure\Mapping;
use PezosSandbox\Infrastructure\Symfony\Form\TagForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/member-area")
 */
final class TagController extends AbstractController
{
    use Mapping;

    private ApplicationInterface $application;
    private TranslatorInterface $translator;

    public function __construct(
        ApplicationInterface $application,
        TranslatorInterface $translator
    ) {
        $this->application = $application;
        $this->translator  = $translator;
    }

    /**
     * @Route("/tags", name="app_tag_list", methods={"GET"})
     */
    public function list(): Response
    {
        $tags = $this->application->listTagsForAdmin();

        return $this->render('member_area/tags/list.html.twig', [
            'tags' => $tags,
        ]);
    }

    /**
     * @Route("/tags/new", name="app_tag_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $form = $this->createform(TagForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            try {
                $addTag = new AddTag($formData['label']);

                $this->application->addTag($addTag);
                $this->addFlash(FlashType::SUCCESS, 'Tag added!');

                return $this->redirectToRoute('app_tag_list');
            } catch (\Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->renderForm('member_area/tags/tag.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/tags/edit/{tagId}", name="app_tag_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request): Response
    {
        $tagId = $request->attributes->get('tagId');

        try {
            $tag = $this->application->getOneTagByTagId($tagId);
        } catch (CouldNotFindTag $exception) {
            $this->convertToFlashMessage($exception);

            return $this->redirectToRoute('app_token_list');
        }

        $form = $this->createForm(TagForm::class, [
            'label' => $tag->label(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            try {
                $updateTag = new UpdateTag($tag->tagId(), $formData['label']);
                $this->application->updateTag($updateTag);
                $this->addFlash(FlashType::SUCCESS, 'Tag edited!');
                $this->redirectToRoute('app_tag_list');
            } catch (\Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->renderForm('member_area/tags/tag.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/tags/delete/{tagId}", name="app_tag_delete", methods={"DELETE"})
     */
    public function delete(Request $request): Response
    {
        $tagId = $request->attributes->get('tagId');

        $removeTag = new RemoveTag($tagId);

        $this->application->removeTag($removeTag);
        $this->addFlash(FlashType::SUCCESS, 'Tag removed!');

        return $this->redirectToRoute('app_tag_list');
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
