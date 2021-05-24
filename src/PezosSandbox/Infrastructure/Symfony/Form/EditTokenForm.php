<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class EditTokenForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('symbol', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('name', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('thumbnailUri', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('decimals', IntegerType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('active', CheckboxType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
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
