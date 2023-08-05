<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Backup;
use App\Entity\Database;
use App\Entity\Enum\S3Provider;
use App\Entity\Enum\S3StorageClass;
use App\Entity\FtpAdapter;
use PHPUnit\Framework\TestCase;

final class FtpAdapterTest extends TestCase
{
    public function testName(): void
    {
        $entity = new FtpAdapter();
        self::assertNull($entity->getName());
        $entity->setName('test');
        self::assertSame('test', $entity->getName());
    }

    public function testPrefix(): void
    {
        $entity = new FtpAdapter();
        self::assertNull($entity->getPrefix());
        $entity->setPrefix('test');
        self::assertSame('test', $entity->getPrefix());
    }

    public function testFtpHost(): void
    {
        $entity = new FtpAdapter();
        self::assertNull($entity->getFtpHost());
        $entity->setFtpHost('host');
        self::assertSame('host', $entity->getFtpHost());
    }

    public function testFtpUsername(): void
    {
        $entity = new FtpAdapter();
        self::assertNull($entity->getFtpUsername());
        $entity->setFtpUsername('username');
        self::assertSame('username', $entity->getFtpUsername());
    }

    public function testFtpPassword(): void
    {
        $entity = new FtpAdapter();
        self::assertNull($entity->getFtpPassword());
        $entity->setFtpPassword('password');
        self::assertSame('password', $entity->getFtpPassword());
    }

    public function testFtpPlainPassword(): void
    {
        $entity = new FtpAdapter();
        self::assertNull($entity->getFtpPlainPassword());
        $entity->setFtpPlainPassword('plain password');
        self::assertSame('plain password', $entity->getFtpPlainPassword());
    }

    public function testFtpPort(): void
    {
        $entity = new FtpAdapter();
        self::assertNull($entity->getFtpPort());
        $entity->setFtpPort(21);
        self::assertSame(21, $entity->getFtpPort());
    }

    public function testToString(): void
    {
        $entity = new FtpAdapter();
        $entity->setName('test');
        self::assertSame('FTP (test)', (string) $entity);
    }

    public function testDbases(): void
    {
        $entity = new FtpAdapter();
        self::assertCount(0, $entity->getDatabases());
        $entity->addDatabase(new Database());
        self::assertCount(1, $entity->getDatabases());
        $entity->removeDatabase($entity->getDatabases()->first());
        self::assertCount(0, $entity->getDatabases());
    }

    public function testGetSavesCount(): void
    {
        $entity = new FtpAdapter();
        self::assertSame(0, $entity->getSavesCount());
        $entity->addDatabase(new Database());
        self::assertSame(0, $entity->getSavesCount());
        $entity->addDatabase((new Database())->addBackup(new Backup()));
        self::assertSame(1, $entity->getSavesCount());
        $entity->removeDatabase($entity->getDatabases()->last());
        self::assertSame(0, $entity->getSavesCount());
    }
}
