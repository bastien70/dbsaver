<?php

declare(strict_types=1);

namespace App\AdapterResolver;

use App\Entity\Backup;
use App\Entity\Enum\S3Provider;
use App\Entity\S3Adapter;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\FilesystemAdapter;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class S3AdapterResolver implements AdapterResolverInterface
{
    public function __construct(
        private readonly S3Adapter $adapterConfig,
        private readonly Encryptor $encryptor,
    ) {
    }

    public function getAdapter(): FilesystemAdapter
    {
        $client = $this->getClient();

        return new AwsS3V3Adapter(
            $client,
            $this->adapterConfig->getS3BucketName(),
            $this->adapterConfig->getPrefix(),
            null,
            null,
            $this->adapterConfig->getStorageClass() ? ['StorageClass' => $this->adapterConfig->getStorageClass()->value] : []
        );
    }

    public function download(Backup $backup): RedirectResponse
    {
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $backup->getBackupFileName()
        );

        $client = $this->getClient();

        $cmd = $client->getCommand('GetObject', [
            'Bucket' => $this->adapterConfig->getS3BucketName(),
            'Key' => \sprintf('%s/%s', $this->adapterConfig->getPrefix(), $backup->getBackupFileName()),
            'ResponseContentType' => $backup->getMimeType(),
            'ResponseContentDisposition' => $disposition,
        ]);

        $request = $client->createPresignedRequest($cmd, '+2 minutes');

        return new RedirectResponse((string) $request->getUri());
    }

    private function getClient(): S3Client
    {
        $clientData = [
            'version' => 'latest',
            'region' => $this->adapterConfig->getS3Region(),
            'credentials' => [
                'key' => $this->adapterConfig->getS3AccessId(),
                'secret' => $this->encryptor->decrypt($this->adapterConfig->getS3AccessSecret()),
            ],
        ];

        if (($provider = $this->adapterConfig->getS3Provider()) !== S3Provider::AMAZON_AWS) {
            // Fill endpoint option by default value if Scaleway. None if AWS, and custom if other.
            $clientData['endpoint'] = match ($provider) {
                S3Provider::SCALEWAY => \sprintf('https://s3.%s.scw.cloud', $this->adapterConfig->getS3Region()),
                S3Provider::OTHER => $this->adapterConfig->getS3Endpoint(),
                default => throw new \Exception('Unexpected adapter provider value'),
            };
        }

        return new S3Client($clientData);
    }
}
