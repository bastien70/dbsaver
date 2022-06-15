<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Backup;
use App\Entity\Database;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

final class BackupTest extends TestCase
{
    public function testId(): void
    {
        $entity = new Backup();
        self::assertNull($entity->getId());
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, 2);
        self::assertSame(2, $entity->getId());
    }

    public function testToString(): void
    {
        $entity = new Backup();
        $database = new Database();
        $database->setName('db_name');
        $entity->setDatabase($database);
        $date = new \DateTimeImmutable();
        $formattedDate = $date->format('d/m/Y H:i:s');
        self::assertSame('db_name - ' . $formattedDate, (string) $entity);
    }

    public function testBackupFileName(): void
    {
        $entity = new Backup();
        self::assertNull($entity->getBackupFileName());
        $entity->setBackupFileName('test');
        self::assertSame('test', $entity->getBackupFileName());
    }

    public function testBackupFile(): void
    {
        $entity = new Backup();
        $file = new File(__FILE__);
        $entity->setBackupFile($file);
        self::assertSame($file, $entity->getBackupFile());
    }

    public function testMimeType(): void
    {
        $entity = new Backup();
        self::assertNull($entity->getMimeType());
        $entity->setMimeType('application/zip');
        self::assertSame('application/zip', $entity->getMimeType());
    }

    public function testBackupFileSize(): void
    {
        $entity = new Backup();
        self::assertNull($entity->getBackupFileSize());
        $entity->setBackupFileSize(105);
        self::assertSame(105, $entity->getBackupFileSize());
    }

    public function testContext(): void
    {
        $entity = new Backup();
        $entity->setContext(Backup::CONTEXT_AUTOMATIC);
        self::assertSame(Backup::CONTEXT_AUTOMATIC, $entity->getContext());
    }

    // serialize,unserialize

    public function testCreatedAt(): void
    {
        $entity = new Backup();
        $date = new \DateTimeImmutable();
        self::assertInstanceOf(\DateTimeImmutable::class, $entity->getCreatedAt());
        $entity->setCreatedAt($date);
        self::assertSame($date, $entity->getCreatedAt());
    }

    public function testUpdatedAt(): void
    {
        $entity = new Backup();
        $date = new \DateTimeImmutable();
        $entity->setUpdatedAt($date);
        self::assertSame($date, $entity->getUpdatedAt());
    }

    public function testDatabase(): void
    {
        $entity = new Backup();
        $database = new Database();
        self::assertNull($entity->getDatabase());
        $entity->setDatabase($database);
        self::assertSame($database, $entity->getDatabase());
    }

    public function testSerialize(): void
    {
        $entity = new Backup();
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, 3);
        self::assertSame('a:1:{i:0;i:3;}', $entity->serialize());
    }

    public function testUnserialize(): void
    {
        $entity = new Backup();
        $entity->unserialize('a:1:{i:0;i:55;}');
        self::assertSame(55, $entity->getId());
    }
}
