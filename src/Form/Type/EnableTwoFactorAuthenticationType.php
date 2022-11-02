<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Model\EnableTwoFactorAuthenticationModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EnableTwoFactorAuthenticationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('code', TextType::class, [
            'label' => 'user.enable_2fa.code',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', EnableTwoFactorAuthenticationModel::class);
    }
}
