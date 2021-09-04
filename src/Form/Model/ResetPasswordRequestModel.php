<?php

declare(strict_types=1);

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class ResetPasswordRequestModel
{
    #[Assert\NotBlank()]
    public ?string $email = null;
}
