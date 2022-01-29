<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class TimeIntervalForm extends AbstractType
{
    public function __construct(
        private RequestStack $requestStack,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('interval', ChoiceType::class, [
            'choices' => [
                '24h'  => '-24 hours',
                '7d'   => '-7 days',
                '14d'  => '-14 days',
                '30d'  => '-30 days',
                '90d'  => '-90 days',
                '180d' => '-180 days',
                '1y'   => '-1 year',
                'Max'  => 'max',
            ],
            'label'    => false,
            'expanded' => true,
            'multiple' => false,
            'data'     => $this->requestStack->getSession()->get('time_interval'),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action' => $this->urlGenerator->generate('_app_time_interval'),
            'attr'   => ['data-time-interval-form-target' => 'form'],
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
