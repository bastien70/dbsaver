<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum S3Provider: string
{
    case AMAZON_AWS = 'amazon_aws';
    case SCALEWAY = 'scaleway';
    case OTHER = 'other';
    public function getText(): string
    {
        return match ($this) {
            self::AMAZON_AWS => 'enum.s3_provider.amazon_aws',
            self::SCALEWAY => 'enum.s3_provider.scaleway',
            self::OTHER => 'enum.s3_provider.other'
        };
    }
}
