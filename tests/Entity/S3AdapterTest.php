<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Backup;
use App\Entity\Database;
use App\Entity\Enum\S3Provider;
use App\Entity\Enum\S3StorageClass;
use App\Entity\S3Adapter;
use PHPUnit\Framework\TestCase;

final class S3AdapterTest extends TestCase
{
    public function testName(): void
    {
        $entity = new S3Adapter();
        self::assertNull($entity->getName());
        $entity->setName('test');
        self::assertSame('test', $entity->getName());
    }

    public function testPrefix(): void
    {
        $entity = new S3Adapter();
        self::assertNull($entity->getPrefix());
        $entity->setPrefix('test');
        self::assertSame('test', $entity->getPrefix());
    }

    public function testS3AccessId(): void
    {
        $entity = new S3Adapter();
        self::assertNull($entity->getS3AccessId());
        $entity->setS3AccessId('test');
        self::assertSame('test', $entity->getS3AccessId());
    }

    public function testS3AccessSecret(): void
    {
        $entity = new S3Adapter();
        self::assertNull($entity->getS3AccessSecret());
        $entity->setS3AccessSecret('test');
        self::assertSame('test', $entity->getS3AccessSecret());
    }

    public function testS3PlainAccessSecret(): void
    {
        $entity = new S3Adapter();
        self::assertNull($entity->getS3PlainAccessSecret());
        $entity->setS3PlainAccessSecret('test');
        self::assertSame('test', $entity->getS3PlainAccessSecret());
    }

    public function testS3BucketName(): void
    {
        $entity = new S3Adapter();
        self::assertNull($entity->getS3BucketName());
        $entity->setS3BucketName('test');
        self::assertSame('test', $entity->getS3BucketName());
    }

    public function testS3Region(): void
    {
        $entity = new S3Adapter();
        self::assertNull($entity->getS3Region());
        $entity->setS3Region('test');
        self::assertSame('test', $entity->getS3Region());
    }

    public function testS3Provider(): void
    {
        $entity = new S3Adapter();
        self::assertNull($entity->getS3Provider());
        $entity->setS3Provider(S3Provider::SCALEWAY);
        self::assertSame(S3Provider::SCALEWAY, $entity->getS3Provider());
    }

    public function testStorageClass(): void
    {
        $entity = new S3Adapter();
        self::assertNull($entity->getStorageClass());
        $entity->setStorageClass(S3StorageClass::SCALEWAY_ONEZONE_IA);
        self::assertSame(S3StorageClass::SCALEWAY_ONEZONE_IA, $entity->getStorageClass());
    }

    public function testS3Endpoint(): void
    {
        $entity = new S3Adapter();
        self::assertNull($entity->getS3Endpoint());
        $entity->setS3Endpoint('test');
        self::assertSame('test', $entity->getS3Endpoint());
    }

    public function testToString(): void
    {
        $entity = new S3Adapter();
        $entity->setName('test');
        self::assertSame('S3 (test)', (string) $entity);
    }

    public function testDbases(): void
    {
        $entity = new S3Adapter();
        self::assertCount(0, $entity->getDatabases());
        $entity->addDatabase(new Database());
        self::assertCount(1, $entity->getDatabases());
        $entity->removeDatabase($entity->getDatabases()->first());
        self::assertCount(0, $entity->getDatabases());
    }

    public function testGetSavesCount(): void
    {
        $entity = new S3Adapter();
        self::assertSame(0, $entity->getSavesCount());
        $entity->addDatabase(new Database());
        self::assertSame(0, $entity->getSavesCount());
        $entity->addDatabase((new Database())->addBackup(new Backup()));
        self::assertSame(1, $entity->getSavesCount());
        $entity->removeDatabase($entity->getDatabases()->last());
        self::assertSame(0, $entity->getSavesCount());
    }
}
