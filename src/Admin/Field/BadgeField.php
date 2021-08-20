<?php

declare(strict_types=1);

namespace App\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

class BadgeField implements FieldInterface
{
    use FieldTrait;

    public const BADGE_CLASS = 'badge_class';
    public const BADGE_BACKGROUND_COLOR = 'badge_background_color';
    public const BADGE_ROUNDED = 'badge_rounded';
    public const BADGE_FONT_SIZE = 'badge_font_size';
    public const BADGE_TEXT_CLASS = 'badge_text_class';

    public static function new(string $propertyName, ?string $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath('bundles/EasyAdminBundle/field/badge.html.twig')
            ->setCustomOption(self::BADGE_CLASS, 'primary')
            ->setCustomOption(self::BADGE_BACKGROUND_COLOR, null)
            ->setCustomOption(self::BADGE_ROUNDED, false)
            ->setCustomOption(self::BADGE_FONT_SIZE, null)
            ->setCustomOption(self::BADGE_TEXT_CLASS, null);
    }

    /**
     * Available classes : primary, secondary, success, danger, warning, info, light, dark.
     */
    public function setBadgeClass(string $class = 'primary'): self
    {
        $this->setCustomOption(self::BADGE_CLASS, $class);

        return $this;
    }

    public function setTextClass(?string $class = null): self
    {
        $this->setCustomOption(self::BADGE_TEXT_CLASS, $class);

        return $this;
    }

    /**
     * Hex color.
     * Example : #0277bd.
     */
    public function setBackgroundColor(?string $backgroundColor = null): self
    {
        $this->setCustomOption(self::BADGE_BACKGROUND_COLOR, $backgroundColor);

        return $this;
    }

    public function setRounded(bool $rounded = true): self
    {
        $this->setCustomOption(self::BADGE_ROUNDED, $rounded);

        return $this;
    }

    public function setFontSize(?string $fontSize = null): self
    {
        $this->setCustomOption(self::BADGE_FONT_SIZE, $fontSize);

        return $this;
    }
}
