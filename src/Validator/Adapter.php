<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Adapter extends Constraint
{
    public string $message = 'Adapter connection test errored: "{{ error }}".';

    /**
     * @return string[]
     */
    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT];
    }
}
