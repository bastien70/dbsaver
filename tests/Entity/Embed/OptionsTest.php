<?php

declare(strict_types=1);

namespace App\Tests\Entity\Embed;

use App\Entity\Embed\Options;
use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{
    public function testResetAutoIncrement(): void
    {
        $options = new Options();
        self::assertFalse($options->isResetAutoIncrement());
        $options->setResetAutoIncrement(true);
        self::assertTrue($options->isResetAutoIncrement());
    }

    public function testAddDropDatabase(): void
    {
        $options = new Options();
        self::assertFalse($options->isAddDropDatabase());
        $options->setAddDropDatabase(true);
        self::assertTrue($options->isAddDropDatabase());
    }

    public function testAddDropTable(): void
    {
        $options = new Options();
        self::assertFalse($options->isAddDropTable());
        $options->setAddDropTable(true);
        self::assertTrue($options->isAddDropTable());
    }

    public function testAddDropTrigger(): void
    {
        $options = new Options();
        self::assertTrue($options->isAddDropTrigger());
        $options->setAddDropTrigger(false);
        self::assertFalse($options->isAddDropTrigger());
    }

    public function testAddLocks(): void
    {
        $options = new Options();
        self::assertTrue($options->isAddLocks());
        $options->setAddLocks(false);
        self::assertFalse($options->isAddLocks());
    }

    public function testCompleteInsert(): void
    {
        $options = new Options();
        self::assertFalse($options->isCompleteInsert());
        $options->setCompleteInsert(true);
        self::assertTrue($options->isCompleteInsert());
    }
}
