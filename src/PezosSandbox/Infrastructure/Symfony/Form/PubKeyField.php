<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Form;

use PezosSandbox\Infrastructure\Symfony\Validation\PubKeyConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class PubKeyField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'form.pub_key.label',
            'help' => 'form.pub_key.help',
            'constraints' => [new NotBlank(), new PubKeyConstraint()],
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}
