<?php

declare(strict_types=1);

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class ResetPasswordRequestModel
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;
}
