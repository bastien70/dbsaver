<?php

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Helper\LocaleHelper;
use PHPUnit\Framework\TestCase;

final class LocaleHelperTest extends TestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testGetLanguageName(string $locale, string $expectedLanguageName): void
    {
        self::assertSame($expectedLanguageName, LocaleHelper::getLanguageName($locale));
    }

    /**
     * @return iterable<string, array<string>>
     */
    public function provideCases(): iterable
    {
        yield 'english' => ['en', 'English'];
        yield 'french' => ['fr', 'Français'];
        yield 'german' => ['de', 'Deutsch'];
    }
}
