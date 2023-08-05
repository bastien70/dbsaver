<?php

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Entity\LocalAdapter;
use App\Helper\FlysystemHelper;
use App\Tests\Fixtures\DataProvider;
use League\Flysystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class FlysystemHelperTest extends KernelTestCase
{
    use Factories;

    private FlysystemHelper $flysystemHelper;
    private DataProvider $dataProvider;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->flysystemHelper = self::getContainer()->get(FlysystemHelper::class);
        $this->dataProvider = self::getContainer()->get(DataProvider::class);
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
        self::assertTrue($this->flysystemHelper->isConnectionOk($this->dataProvider->getValidS3Adapter()));
    }

    public function testS3ConnectionNotOk(): void
    {
        self::assertFalse($this->flysystemHelper->isConnectionOk($this->dataProvider->getInvalidS3Adapter()));
    }

    public function testFtpConnectionOk(): void
    {
        self::assertTrue($this->flysystemHelper->isConnectionOk($this->dataProvider->getValidFtpAdapter()));
    }

    public function testFtpConnectionNotOk(): void
    {
        self::assertFalse($this->flysystemHelper->isConnectionOk($this->dataProvider->getInvalidFtpAdapter()));
    }

    public function testGetFlysystemAdapter(): void
    {
        self::assertInstanceOf(
            Filesystem::class,
            $this->flysystemHelper->getFileSystem($this->dataProvider->getValidS3Adapter())
        );

        self::assertInstanceOf(
            Filesystem::class,
            $this->flysystemHelper->getFileSystem($this->dataProvider->getLocalS3Adapter())
        );

        self::assertInstanceOf(
            Filesystem::class,
            $this->flysystemHelper->getFileSystem($this->dataProvider->getValidFtpAdapter())
        );
    }

    public function testUploadDownloadRemoveValidS3Adapter(): void
    {
        $backup = $this->dataProvider->getBackupFromS3Adapter();
        $this->flysystemHelper->upload($backup);
        self::assertStringContainsString('CREATE TABLE `post`', $this->flysystemHelper->getContent($backup));
        self::assertInstanceOf(Response::class, $this->flysystemHelper->download($backup));
        $this->flysystemHelper->remove($backup);
    }

    public function testUploadDownloadRemoveValidFtpAdapter(): void
    {
        $backup = $this->dataProvider->getBackupFromFtpAdapter();
        $this->flysystemHelper->upload($backup);
        self::assertStringContainsString('CREATE TABLE `post`', $this->flysystemHelper->getContent($backup));
        self::assertInstanceOf(Response::class, $this->flysystemHelper->download($backup));
        $this->flysystemHelper->remove($backup);
    }

    public function testUploadDownloadRemoveValidLocalAdapter(): void
    {
        $backup = $this->dataProvider->getBackupFromLocalAdapter();
        $this->flysystemHelper->upload($backup);
        self::assertStringContainsString('CREATE TABLE `post`', $this->flysystemHelper->getContent($backup));
        self::assertInstanceOf(Response::class, $this->flysystemHelper->download($backup));
        $this->flysystemHelper->remove($backup);
    }
}
