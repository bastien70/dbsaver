<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Backup;
use App\Entity\Database;
use App\Entity\LocalAdapter;
use PHPUnit\Framework\TestCase;

final class LocalAdapterTest extends TestCase
{
    public function testName(): void
    {
        $entity = new LocalAdapter();
        self::assertNull($entity->getName());
        $entity->setName('test');
        self::assertSame('test', $entity->getName());
    }

    public function testPrefix(): void
    {
        $entity = new LocalAdapter();
        self::assertNull($entity->getPrefix());
        $entity->setPrefix('test');
        self::assertSame('test', $entity->getPrefix());
    }

    public function testDbases(): void
    {
        $entity = new LocalAdapter();
        self::assertCount(0, $entity->getDatabases());
        $entity->addDatabase(new Database());
        self::assertCount(1, $entity->getDatabases());
        $entity->removeDatabase($entity->getDatabases()->first());
        self::assertCount(0, $entity->getDatabases());
    }

    public function testGetSavesCount(): void
    {
        $entity = new LocalAdapter();
        self::assertSame(0, $entity->getSavesCount());
        $entity->addDatabase(new Database());
        self::assertSame(0, $entity->getSavesCount());
        $entity->addDatabase((new Database())->addBackup(new Backup()));
        self::assertSame(1, $entity->getSavesCount());
        $entity->removeDatabase($entity->getDatabases()->last());
        self::assertSame(0, $entity->getSavesCount());
    }

    public function testToString(): void
    {
        $entity = new LocalAdapter();
        $entity->setName('test');
        self::assertSame('Local (test)', $entity->__toString());
    }
}
