<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum S3StorageClass: string
{
    case STANDARD = 'STANDARD';
    case AWS_INTELLIGENT_TIERCING = 'INTELLIGENT_TIERCING';
    case SCALEWAY_ONEZONE_IA = 'ONEZONE_IA';
    case SCALEWAY_GLACIER = 'GLACIER';

    /**
     * @return S3StorageClass[]
     */
    public static function getAwsStorageClasses(): array
    {
        return [
            self::STANDARD,
            self::AWS_INTELLIGENT_TIERCING,
        ];
    }

    /**
     * @return S3StorageClass[]
     */
    public static function getScalewayStorageClasses(): array
    {
        return [
            self::STANDARD,
            self::SCALEWAY_ONEZONE_IA,
            self::SCALEWAY_GLACIER,
        ];
    }
}
