<?php

declare(strict_types=1);

namespace App\AdapterResolver;

use App\Entity\Backup;
use App\Entity\FtpAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use League\Flysystem\Ftp\FtpConnectionProvider;
use League\Flysystem\Ftp\NoopCommandConnectivityChecker;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FtpAdapterResolver implements AdapterResolverInterface
{
    public function __construct(
        private readonly FtpAdapter $adapterConfig,
        private readonly Encryptor $encryptor,
    ) {
    }

    public function getAdapter(): FilesystemAdapter
    {
        return new \League\Flysystem\Ftp\FtpAdapter(
            FtpConnectionOptions::fromArray([
                'host' => $this->adapterConfig->getFtpHost(),
                'root' => $this->adapterConfig->getPrefix(),
                'ssl' => $this->adapterConfig->isFtpSsl(),
                'port' => $this->adapterConfig->getFtpPort(),
                'username' => $this->adapterConfig->getFtpUsername(),
                'password' => $this->encryptor->decrypt($this->adapterConfig->getFtpPassword()),
            ]),
            new FtpConnectionProvider(),
            new NoopCommandConnectivityChecker(),
            new PortableVisibilityConverter()
        );
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
