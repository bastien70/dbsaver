<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AdapterConfig;
use App\Entity\Backup;
use App\Entity\Database;
use PHPUnit\Framework\TestCase;

final class AdapterConfigTest extends TestCase
{
    public function testId(): void
    {
        $entity = new AdapterConfig();
        self::assertNull($entity->getId());
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, 2);
        self::assertSame(2, $entity->getId());
    }

    public function testName(): void
    {
        $entity = new AdapterConfig();
        self::assertNull($entity->getName());
        $entity->setName('test');
        self::assertSame('test', $entity->getName());
    }

    public function testPrefix(): void
    {
        $entity = new AdapterConfig();
        self::assertNull($entity->getPrefix());
        $entity->setPrefix('test');
        self::assertSame('test', $entity->getPrefix());
    }

    public function testDbases(): void
    {
        $entity = new AdapterConfig();
        self::assertCount(0, $entity->getDatabases());
        $entity->addDatabase(new Database());
        self::assertCount(1, $entity->getDatabases());
        $entity->removeDatabase($entity->getDatabases()->first());
        self::assertCount(0, $entity->getDatabases());
    }

    public function testGetSavesCount(): void
    {
        $entity = new AdapterConfig();
        self::assertSame(0, $entity->getSavesCount());
        $entity->addDatabase(new Database());
        self::assertSame(0, $entity->getSavesCount());
        $entity->addDatabase((new Database())->addBackup(new Backup()));
        self::assertSame(1, $entity->getSavesCount());
        $entity->removeDatabase($entity->getDatabases()->last());
        self::assertSame(0, $entity->getSavesCount());
    }
}
