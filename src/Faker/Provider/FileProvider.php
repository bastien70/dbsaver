<?php

declare(strict_types=1);

namespace App\Faker\Provider;

use Faker\Provider\Base as BaseProvider;
use Symfony\Component\HttpFoundation\File\File;

final class FileProvider extends BaseProvider
{
    public function getSqlFile(): File
    {
        return new File(__DIR__ . '/fixture.sql');
    }
}
