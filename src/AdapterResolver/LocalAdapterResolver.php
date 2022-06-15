<?php

declare(strict_types=1);

namespace App\AdapterResolver;

use App\Entity\Backup;
use App\Entity\LocalAdapter;
use function fopen;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use function sprintf;
use function stream_copy_to_stream;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class LocalAdapterResolver implements AdapterResolverInterface
{
    public function __construct(private readonly LocalAdapter $adapterConfig)
    {
    }

    public function getAdapter(): FilesystemAdapter
    {
        return new LocalFilesystemAdapter(__DIR__ . sprintf('/../../var/uploads/%s', $this->adapterConfig->getPrefix()));
    }

    public function download(Backup $backup): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($backup) {
            $adapter = $this->getAdapter();
            $filesystem = new Filesystem($adapter);
            $outputStream = fopen('php://output', 'w');
            $fileStream = $filesystem->readStream($backup->getBackupFileName());
            stream_copy_to_stream($fileStream, $outputStream);
        });

        $response->headers->set('Content-Type', $backup->getMimeType());

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $backup->getBackupFileName()
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
