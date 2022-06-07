<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Model\SettingsModel;
use App\Helper\LocaleHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SettingsType extends AbstractType
{
    /**
     * @param array<string> $enabledLocales
     */
    public function __construct(private readonly array $enabledLocales)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('locale', ChoiceType::class, [
                'label' => 'user.settings.locale',
                'choices' => $this->enabledLocales,
                'choice_label' => static function (string $locale): string {
                    return LocaleHelper::getLanguageName($locale);
                },
                'choice_translation_domain' => false,
            ])
            ->add('currentPassword', PasswordType::class, [
                'label' => 'user.settings.current_password',
                'required' => false,
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'user.settings.new_password',
                'required' => false,
            ])
            ->add('receiveAutomaticEmails', CheckboxType::class, [
                'label' => 'user.settings.receive_automatic_emails',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', SettingsModel::class);
    }
}
