<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Component\Intl\Languages;

final class LocaleHelper
{
    public static function getLanguageName(string $locale): string
    {
        return ucfirst(Languages::getName($locale, $locale));
    }
}
