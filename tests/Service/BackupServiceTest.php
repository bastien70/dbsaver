<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Database;
use App\Factory\DatabaseFactory;
use App\Factory\LocalAdapterFactory;
use App\Factory\UserFactory;
use App\Repository\DatabaseRepository;
use App\Service\BackupService;
use App\Tests\Fixtures\DataProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

class BackupServiceTest extends KernelTestCase
{
    use Factories;

    private EntityManagerInterface $manager;
    private DatabaseRepository $databaseRepository;
    private BackupService $backupService;
    private DataProvider $dataProvider;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->manager = self::getContainer()->get(EntityManagerInterface::class);
        $this->databaseRepository = self::getContainer()->get(DatabaseRepository::class);
        $this->backupService = self::getContainer()->get(BackupService::class);
        $this->dataProvider = self::getContainer()->get(DataProvider::class);
    }

    public function testGetDatabasesToBackup(): void
    {
        self::assertCount(4, $this->databaseRepository->getDatabasesToBackup());

        $user = UserFactory::createOne(['email' => 'usertestbackup@test.com']);
        $localAdapter = LocalAdapterFactory::createOne();

        $database = DatabaseFactory::new()->withOwner($user)->withAdapter($localAdapter)->create()->object();
        \assert($database instanceof Database);

        self::assertCount(5, $this->databaseRepository->getDatabasesToBackup());

        $database->getBackupTask()->setNextIteration(new \DateTime('+1 day'));
        $this->manager->flush();
        self::assertCount(4, $this->databaseRepository->getDatabasesToBackup());
    }

    public function testImportBackupWithLocalAdapter(): void
    {
        $backup = $this->dataProvider->getBackupFromLocalAdapter('test_db');
        $this->backupService->import($backup);
        self::expectNotToPerformAssertions();
    }

    public function testImportBackupWithS3Adapter(): void
    {
        $backup = $this->dataProvider->getBackupFromS3Adapter('test_db');
        $this->backupService->import($backup);
        self::expectNotToPerformAssertions();
    }
}
