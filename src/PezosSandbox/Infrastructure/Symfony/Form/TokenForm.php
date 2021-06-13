<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Form;

use PezosSandbox\Infrastructure\Symfony\Validation\TokenMetadataConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class TokenForm extends AbstractType
{
    private $transformer;

    public function __construct(StringToJsonTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contract', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('id', NumberType::class)
            ->add('metadata', TextareaType::class, [
                'constraints' => [new TokenMetadataConstraint()],
                'attr'        => [
                    'data-token-form-target' => 'metadata',
                    'rows'                   => 16,
                ],
            ])
            ->add('active', CheckboxType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
                'attr'  => ['class' => 'btn btn-primary'],
            ])
            ->getForm();

        $builder->get('metadata')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
