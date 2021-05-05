<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class LoginForm extends AbstractType
{
    private UrlGeneratorInterface $urlGenerator;
    private AuthenticationUtils $authenticationUtils;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        AuthenticationUtils $authenticationUtils
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->authenticationUtils = $authenticationUtils;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', TextType::class)
            ->add('_password', PasswordType::class)
            ->add('login', SubmitType::class, [
                'label' => 'login_form.login.label',
            ]);

        $authUtils = $this->authenticationUtils;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (
            FormEvent $event
        ) use ($authUtils) {
            $error = $authUtils->getLastAuthenticationError();

            if ($error) {
                $event
                    ->getForm()
                    ->addError(new FormError($error->getMessage()));
            }

            $event->setData(
                array_replace((array) $event->getData(), [
                    '_username' => $authUtils->getLastUsername(),
                ]),
            );
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action' => $this->urlGenerator->generate('app_login'),
            'attr' => ['novalidate' => 'novalidate'],
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id' => 'authenticate',
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
