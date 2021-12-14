<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Validator\Database;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;

final class DatabaseTest extends TestCase
{
    public function testGetTargets(): void
    {
        self::assertSame([Constraint::CLASS_CONSTRAINT], (new Database())->getTargets());
    }
}
