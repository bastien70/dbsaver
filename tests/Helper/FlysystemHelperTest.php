<?php

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Entity\Backup;
use App\Entity\Database;
use App\Entity\Enum\BackupTaskPeriodicity;
use App\Entity\Enum\S3Provider;
use App\Entity\LocalAdapter;
use App\Entity\S3Adapter;
use App\Factory\BackupFactory;
use App\Factory\UserFactory;
use App\Helper\FlysystemHelper;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\Filesystem;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class FlysystemHelperTest extends KernelTestCase
{
    use Factories;
    private FlysystemHelper $flysystemHelper;
    private Encryptor $encryptor;
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->flysystemHelper = self::getContainer()->get(FlysystemHelper::class);
        $this->encryptor = self::getContainer()->get(Encryptor::class);
        $this->manager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testLocalConnectionOk(): void
    {
        $localAdapter = (new LocalAdapter())
            ->setName('local')
            ->setPrefix('backups');

        self::assertTrue($this->flysystemHelper->isConnectionOk($localAdapter));
    }

    public function testS3ConnectionOk(): void
    {
        self::assertTrue($this->flysystemHelper->isConnectionOk($this->getValidS3Adapter()));
    }

    public function testS3ConnectionNotOk(): void
    {
        self::assertFalse($this->flysystemHelper->isConnectionOk($this->getInvalidS3Adapter()));
    }

    public function testGetFlysystemAdapter(): void
    {
        self::assertInstanceOf(
            Filesystem::class,
            $this->flysystemHelper->getFileSystem($this->getValidS3Adapter())
        );

        self::assertInstanceOf(
            Filesystem::class,
            $this->flysystemHelper->getFileSystem($this->getLocalS3Adapter())
        );
    }

    public function testUploadDownloadRemoveValidS3Adapter(): void
    {
        $backup = $this->getBackupFromS3Adapter();
        $this->flysystemHelper->upload($backup);
        self::assertInstanceOf(Response::class, $this->flysystemHelper->download($backup));
        $this->flysystemHelper->remove($backup);
    }

    public function testUploadDownloadRemoveValidLocalAdapter(): void
    {
        $backup = $this->getBackupFromLocalAdapter();
        $this->flysystemHelper->upload($backup);
        self::assertInstanceOf(Response::class, $this->flysystemHelper->download($backup));
        $this->flysystemHelper->remove($backup);
    }

    private function getValidS3Adapter(): S3Adapter
    {
        return (new S3Adapter())
            ->setName('minio')
            ->setPrefix('backups')
            ->setS3BucketName('somebucketname')
            ->setS3AccessId('minio')
            ->setS3AccessSecret($this->encryptor->encrypt('minio123'))
            ->setS3Provider(S3Provider::OTHER)
            ->setS3Endpoint('http://127.0.0.1:9004')
            ->setS3Region('eu-east-1');
    }

    private function getInvalidS3Adapter(): S3Adapter
    {
        return (new S3Adapter())
            ->setName('minio')
            ->setPrefix('backups')
            ->setS3BucketName('somebucketname')
            ->setS3AccessId('minio')
            ->setS3AccessSecret($this->encryptor->encrypt('bad_access_secret'))
            ->setS3Provider(S3Provider::OTHER)
            ->setS3Endpoint('http://127.0.0.1:9004')
            ->setS3Region('eu-east-1');
    }

    private function getLocalS3Adapter(): LocalAdapter
    {
        return (new LocalAdapter())
            ->setName('local')
            ->setPrefix('backups');
    }

    private function getBackupFromS3Adapter(): Backup
    {
        $s3Adapter = $this->getValidS3Adapter();

        $this->manager->persist($s3Adapter);

        $database = (new Database())
            ->setHost('127.0.0.1')
            ->setUser('root')
            ->setPassword($this->encryptor->encrypt('root'))
            ->setPort(3307)
            ->setName('dbsaver_test')
            ->setMaxBackups(5)
            ->setAdapter($s3Adapter)
            ->setOwner(UserFactory::random()->object());

        $this->setBackupTask($database);

        $this->manager->persist($database);

        return BackupFactory::new()
            ->withDatabase($database)
            ->create()
            ->object();
    }

    private function getBackupFromLocalAdapter(): Backup
    {
        $localAdapter = $this->getLocalS3Adapter();

        $this->manager->persist($localAdapter);

        $database = (new Database())
            ->setHost('127.0.0.1')
            ->setUser('root')
            ->setPassword($this->encryptor->encrypt('root'))
            ->setPort(3307)
            ->setName('dbsaver_test')
            ->setMaxBackups(5)
            ->setAdapter($localAdapter)
            ->setOwner(UserFactory::random()->object());

        $this->setBackupTask($database);

        $this->manager->persist($database);

        return BackupFactory::new()
            ->withDatabase($database)
            ->create()
            ->object();
    }

    private function setBackupTask(Database $database): void
    {
        $backupTask = $database->getBackupTask();
        $backupTask->setPeriodicity(BackupTaskPeriodicity::WEEK)
            ->setPeriodicityNumber(1)
            ->setStartFrom(new \DateTime('-1 day'))
            ->setNextIteration(new \DateTime('-1 day'));
    }
}
