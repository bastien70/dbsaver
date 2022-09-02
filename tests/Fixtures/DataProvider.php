<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Entity\Backup;
use App\Entity\Database;
use App\Entity\Embed\Options;
use App\Entity\Enum\BackupTaskPeriodicity;
use App\Entity\Enum\S3Provider;
use App\Entity\LocalAdapter;
use App\Entity\S3Adapter;
use App\Factory\BackupFactory;
use App\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;

class DataProvider
{
    public function __construct(
        private readonly Encryptor $encryptor,
        private readonly EntityManagerInterface $manager
    ) {
    }

    public function getValidS3Adapter(): S3Adapter
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

    public function getInvalidS3Adapter(): S3Adapter
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

    public function getLocalS3Adapter(): LocalAdapter
    {
        return (new LocalAdapter())
            ->setName('local')
            ->setPrefix('backups');
    }

    public function getBackupFromS3Adapter(string $dbName = 'dbsaver_test'): Backup
    {
        $s3Adapter = $this->getValidS3Adapter();

        $this->manager->persist($s3Adapter);

        $database = (new Database())
            ->setHost('127.0.0.1')
            ->setUser('root')
            ->setPassword($this->encryptor->encrypt('root'))
            ->setPort(3307)
            ->setName($dbName)
            ->setMaxBackups(5)
            ->setAdapter($s3Adapter)
            ->setOwner(UserFactory::random()->object())
            ->setOptions(
                (new Options())
                    ->setAddDropDatabase(true)
                    ->setAddDropTable(true)
            );

        $this->setBackupTask($database);

        $this->manager->persist($database);
        $this->manager->flush();

        return BackupFactory::new()
            ->withDatabase($database)
            ->create()
            ->object();
    }

    public function getBackupFromLocalAdapter(string $dbName = 'dbsaver_test'): Backup
    {
        $localAdapter = $this->getLocalS3Adapter();

        $this->manager->persist($localAdapter);

        $database = (new Database())
            ->setHost('127.0.0.1')
            ->setUser('root')
            ->setPassword($this->encryptor->encrypt('root'))
            ->setPort(3307)
            ->setName($dbName)
            ->setMaxBackups(5)
            ->setAdapter($localAdapter)
            ->setOwner(UserFactory::random()->object())
            ->setOptions(
                (new Options())
                    ->setAddDropDatabase(true)
                    ->setAddDropTable(true)
            );

        $this->setBackupTask($database);

        $this->manager->persist($database);

        return BackupFactory::new()
            ->withDatabase($database)
            ->create()
            ->object();
    }

    public function setBackupTask(Database $database): void
    {
        $backupTask = $database->getBackupTask();
        $backupTask->setPeriodicity(BackupTaskPeriodicity::WEEK)
            ->setPeriodicityNumber(1)
            ->setStartFrom(new \DateTime('-1 day'))
            ->setNextIteration(new \DateTime('-1 day'));
    }
}
