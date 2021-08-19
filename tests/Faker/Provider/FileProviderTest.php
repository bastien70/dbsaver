<?php

declare(strict_types=1);

namespace App\Tests\Faker\Provider;

use App\Faker\Provider\FileProvider;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

final class FileProviderTest extends TestCase
{
    private FileProvider $fileProvider;

    protected function setUp(): void
    {
        $this->fileProvider = new FileProvider(new Generator());
    }

    public function testGetSqlFile(): void
    {
        $file = $this->fileProvider->getSqlFile();
        self::assertStringContainsString('CREATE TABLE user', $file->getContent());
    }
}
