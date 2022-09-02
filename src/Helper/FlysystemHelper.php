<?php

declare(strict_types=1);

namespace App\Helper;

use App\AdapterResolver\LocalAdapterResolver;
use App\AdapterResolver\S3AdapterResolver;
use App\Entity\AdapterConfig;
use App\Entity\Backup;
use App\Entity\LocalAdapter;
use App\Entity\S3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use function random_int;
use RuntimeException;
use function sprintf;
use Symfony\Component\HttpFoundation\Response;

final class FlysystemHelper
{
    private ?string $lastExceptionMessage = null;

    public function __construct(
        private readonly Encryptor $encryptor,
        private readonly string $projectDir,
    ) {
    }

    public function isConnectionOk(AdapterConfig $adapterConfig): bool
    {
        try {
            $adapter = $this->getAdapter($adapterConfig);
            $filesystem = new Filesystem($adapter);
            $fileNameTest = sprintf('dbsaver-check-%s.txt', random_int(1, 9999));
            $filesystem->write($fileNameTest, 'DbSaver test file. Will be immediately removed.');
            $filesystem->delete($fileNameTest);

            return true;
        } catch (FilesystemException $e) {
            $this->lastExceptionMessage = $e->getMessage();

            return false;
        }
    }

    public function getLastExceptionMessage(): ?string
    {
        return $this->lastExceptionMessage;
    }

    public function getFileSystem(AdapterConfig $adapterConfig): Filesystem
    {
        $adapter = $this->getAdapter($adapterConfig);

        return new Filesystem($adapter);
    }

    /**
     * @throws FilesystemException
     */
    public function remove(Backup $backup): void
    {
        $filesystem = $this->getFileSystem($backup->getDatabase()->getAdapter());
        $filesystem->delete($backup->getBackupFileName());
    }

    /**
     * @throws FilesystemException
     */
    public function upload(Backup $backup): void
    {
        $adapter = $this->getAdapter($backup->getDatabase()->getAdapter());
        $filesystem = new Filesystem($adapter);
        $filesystem->write($backup->getBackupFileName(), $backup->getBackupFile()->getContent());
    }

    public function getContent(Backup $backup): string
    {
        $adapter = $this->getAdapter($backup->getDatabase()->getAdapter());
        $filesystem = new Filesystem($adapter);

        return $filesystem->read($backup->getBackupFileName());
    }

    public function download(Backup $backup): Response
    {
        $adapterConfig = $backup->getDatabase()->getAdapter();

        return match (true) {
            $adapterConfig instanceof LocalAdapter => (new LocalAdapterResolver($adapterConfig, $this->projectDir))->download($backup),
            $adapterConfig instanceof S3Adapter => (new S3AdapterResolver($adapterConfig, $this->encryptor))->download($backup),
            default => throw new RuntimeException('Adapter not supported'),
        };
    }

    public function getAdapter(AdapterConfig $adapterConfig): FilesystemAdapter
    {
        return match (true) {
            $adapterConfig instanceof S3Adapter => (new S3AdapterResolver($adapterConfig, $this->encryptor))->getAdapter(),
            $adapterConfig instanceof LocalAdapter => (new LocalAdapterResolver($adapterConfig, $this->projectDir))->getAdapter(),
            default => throw new RuntimeException('Adapter not supported'),
        };
    }
}
