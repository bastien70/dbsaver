<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Totp extends Constraint
{
    public string $message = 'The code is invalid.';

    /**
     * @return array<string>
     */
    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT];
    }
}
