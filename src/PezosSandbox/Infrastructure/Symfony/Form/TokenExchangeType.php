<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Form;

use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\Exchanges\Exchange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class TokenExchangeType extends AbstractType
{
    private ApplicationInterface $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('exchangeId', ChoiceType::class, [
                'choices' => $this->exchangeChoices(),
                'label'   => false,
            ])
            ->add('contract', TextType::class);
    }

    private function exchangeChoices(): array
    {
        return array_reduce(
            array_map(
                fn (Exchange $exchange): array => [
                    $exchange->name() => $exchange->exchangeId()->asString(),
                ],
                $this->application->listExchanges()
            ),
            fn (array $acc, array $exchange): array => $acc + $exchange,
            []
        );
    }
}
