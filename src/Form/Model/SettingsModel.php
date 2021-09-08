<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\User;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class SettingsModel
{
    #[Assert\NotBlank]
    #[Assert\Locale]
    public ?string $locale = null;

    public ?string $currentPassword = null;

    public ?string $newPassword = null;

    public bool $receiveAutomaticEmails;

    #[Assert\Callback]
    public function validatePassword(ExecutionContextInterface $context): void
    {
        if (null !== $this->newPassword) {
            $context->getValidator()
                ->inContext($context)
                ->atPath('currentPassword')
                ->validate($this->currentPassword, new Assert\NotBlank());
        }

        if (null !== $this->currentPassword) {
            $context->getValidator()
                ->inContext($context)
                ->atPath('currentPassword')
                ->validate($this->currentPassword, new SecurityAssert\UserPassword());
        }
    }

    public static function createFromUser(User $user): self
    {
        $model = new self();
        $model->locale = $user->getLocale();
        $model->receiveAutomaticEmails = $user->getReceiveAutomaticEmails();

        return $model;
    }
}
