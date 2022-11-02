<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\User;
use App\Validator\Totp;
use Symfony\Component\Validator\Constraints as Assert;

#[Totp]
final class EnableTwoFactorAuthenticationModel
{
    #[Assert\NotBlank]
    public ?string $code = null;

    public function __construct(public readonly User $user)
    {
    }
}
