<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Validator\Adapter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;

final class AdapterTest extends TestCase
{
    public function testGetTargets(): void
    {
        self::assertSame([Constraint::CLASS_CONSTRAINT], (new Adapter())->getTargets());
    }
}
