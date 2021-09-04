<?php

declare(strict_types=1);

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordModel
{
    #[Assert\NotBlank()]
    public ?string $plainPassword = null;
}
