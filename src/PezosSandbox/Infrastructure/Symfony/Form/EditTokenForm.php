<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class EditTokenForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('addressQuipuswap', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('symbol', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('name', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('decimals', IntegerType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('supplyAdjustment', IntegerType::class)
            ->add('kind', ChoiceType::class, ['choices'=>['FA1.2' => 'FA1.2', 'FA2' => 'FA2']])
            ->add('homepage', TextType::class)
            ->add('description', TextareaType::class)
            ->add('social', TextareaType::class)
            ->add('thumbnailUri', TextType::class)
            ->add('active', CheckboxType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
                'attr'  => ['class' => 'btn btn-primary'],
            ])
            ->getForm();
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
