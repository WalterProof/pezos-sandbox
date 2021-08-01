<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Form;

use PezosSandbox\Application\Tags\Tag;
use PezosSandbox\Infrastructure\Symfony\Validation\TokenMetadataConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class TokenForm extends AbstractType
{
    private StringToJsonTransformer $stringToJsonTransformer;

    public function __construct(
        StringToJsonTransformer $stringToJsonTransformer
    ) {
        $this->stringToJsonTransformer = $stringToJsonTransformer;
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
                    'placeholder'            => json_encode([
                        'decimals' => null,
                        'symbol'   => null,
                        'name'     => null,
                    ]),
                ],
            ])
            ->add('active', CheckboxType::class)
            ->add('exchanges', CollectionType::class, [
                'entry_type'   => TokenExchangeType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'attr'         => ['data-token-form-target' => 'collection'],
                'label'        => false,
            ])
            ->add('tags', ChoiceType::class, [
                'choices' => $options['tags'], 'multiple' => true, 'expanded' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
                'attr'  => ['class' => 'btn btn-primary'],
            ])
            ->getForm();

        $builder
            ->get('metadata')
            ->addModelTransformer($this->stringToJsonTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'tags' => [],
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);

        $resolver->setAllowedTypes('tags', 'array');
        $resolver->setNormalizer('tags', static function (Options $options, $tags) {
            if (null === $tags) {
                return $tags;
            }

            return array_reduce(array_map(fn (Tag $tag): array => [$tag->label() => $tag->tagId()], $tags), fn (array $acc, array $tag): array => array_merge($acc, $tag), []);
        });
    }
}
