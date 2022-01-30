<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ChartForm extends AbstractType
{
    public function __construct(
        private RequestStack $requestStack,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('chart_kind', ChoiceType::class, [
            'choices' => [
                'Prices'          => 'prices',
                'QuipuSwap Pools' => 'pools',
            ],
            'label'    => false,
            'expanded' => true,
            'multiple' => false,
            'data'     => $this->requestStack->getSession()->get('chart_kind'),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action' => $this->urlGenerator->generate('_app_chart'),
            'attr'   => ['data-persistent-choice-form-target' => 'form'],
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
