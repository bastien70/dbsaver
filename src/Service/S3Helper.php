<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Backup;
use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\HeaderUtils;

class S3Helper
{
    public function __construct(
        private readonly S3Client $s3Client,
        private readonly string $s3BucketName,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function generatePresignedUri(Backup $backup, string $expires = '+ 30 minutes'): string
    {
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $backup->getBackupFileName()
        );

        $cmd = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $this->s3BucketName,
            'Key' => sprintf('backups/%s', $backup->getBackupFileName()),
            'ResponseContentType' => $backup->getMimeType(),
            'ResponseContentDisposition' => $disposition,
        ]);

        $request = $this->s3Client->createPresignedRequest($cmd, $expires);

        return (string) $request->getUri();
    }
}
