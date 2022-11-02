<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\User;
use App\Validator\Totp;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class EnableTwoFactorAuthenticationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('totpSecret', HiddenType::class)
            ->add('code', TextType::class, [
                'label' => 'user.enable_2fa.code',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Totp(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', User::class);
    }
}
