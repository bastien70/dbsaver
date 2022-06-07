<?php

declare(strict_types=1);

namespace App\Faker;

use App\Faker\Provider\FileProvider;
use Faker\Factory;
use Faker\Generator;

final class FakerGenerator
{
    public static function create(): Generator
    {
        $generator = Factory::create();
        $generator->addProvider(new FileProvider($generator));

        return $generator;
    }
}
