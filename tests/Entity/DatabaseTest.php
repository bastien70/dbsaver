<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Backup;
use App\Entity\Database;
use App\Entity\LocalAdapter;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class DatabaseTest extends TestCase
{
    public function testId(): void
    {
        $entity = new Database();
        self::assertNull($entity->getId());
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, 9);
        self::assertSame(9, $entity->getId());
    }

    public function testToString(): void
    {
        $entity = new Database();
        $entity->setName('test');
        self::assertSame('test', (string) $entity);
    }

    public function testHost(): void
    {
        $entity = new Database();
        $entity->setHost('localhost');
        self::assertSame('localhost', $entity->getHost());
    }

    public function testPort(): void
    {
        $entity = new Database();
        self::assertNull($entity->getPort());
        $entity->setPort(3307);
        self::assertSame(3307, $entity->getPort());
    }

    public function testUser(): void
    {
        $entity = new Database();
        $entity->setUser('admin');
        self::assertSame('admin', $entity->getUser());
    }

    public function testPassword(): void
    {
        $entity = new Database();
        $entity->setPassword('redacted');
        self::assertSame('redacted', $entity->getPassword());
    }

    public function testName(): void
    {
        $entity = new Database();
        $entity->setName('db_name');
        self::assertSame('db_name', $entity->getName());
    }

    public function testMaxBackups(): void
    {
        $entity = new Database();
        $entity->setMaxBackups(3);
        self::assertSame(3, $entity->getMaxBackups());
    }

    public function testPlainPassword(): void
    {
        $entity = new Database();
        self::assertNull($entity->getPlainPassword());
        $entity->setPlainPassword('random');
        self::assertSame('random', $entity->getPlainPassword());
    }

    public function testCreatedAt(): void
    {
        $entity = new Database();
        $date = new \DateTimeImmutable();
        self::assertInstanceOf(\DateTimeImmutable::class, $entity->getCreatedAt());
        $entity->setCreatedAt($date);
        self::assertSame($date, $entity->getCreatedAt());
    }

    public function testOwner(): void
    {
        $entity = new Database();
        $user = new User();
        self::assertNull($entity->getOwner());
        $entity->setOwner($user);
        self::assertSame($user, $entity->getOwner());
    }

    public function testDsn(): void
    {
        $entity = new Database();
        $entity->setHost('localhost')
            ->setPort(3307)
            ->setUser('admin')
            ->setName('db_name');

        self::assertSame('mysql:host=localhost:3307;dbname=db_name', $entity->getDsn());

        $entity = new Database();
        $entity->setHost('localhost')
            ->setUser('admin')
            ->setName('db_name');

        self::assertSame('mysql:host=localhost;dbname=db_name', $entity->getDsn());
    }

    public function testDisplayDsn(): void
    {
        $entity = new Database();
        $entity->setHost('localhost')
            ->setPort(3307)
            ->setUser('admin')
            ->setName('db_name');

        self::assertSame('admin@localhost:3307/db_name', $entity->getDisplayDsn());

        $entity = new Database();
        $entity->setHost('localhost')
            ->setUser('admin')
            ->setName('db_name');

        self::assertSame('admin@localhost/db_name', $entity->getDisplayDsn());
    }

    public function testBackups(): void
    {
        $entity = new Database();
        $backup = new Backup();
        self::assertCount(0, $entity->getBackups());
        self::assertNull($backup->getDatabase());

        $entity->addBackup($backup);
        self::assertCount(1, $entity->getBackups());
        self::assertSame($backup, $entity->getBackups()[0]);
        self::assertSame($entity, $backup->getDatabase());

        $entity->removeBackup($backup);
        self::assertCount(0, $entity->getBackups());
        self::assertNull($backup->getDatabase());
    }

    public function testAdapter(): void
    {
        $entity = new Database();
        self::assertNull($entity->getAdapter());
        $adapter = new LocalAdapter();
        $entity->setAdapter($adapter);
        self::assertSame($adapter, $entity->getAdapter());
    }

    public function testResetAutoIncrement(): void
    {
        $entity = new Database();
        self::assertFalse($entity->isResetAutoIncrement());
        $entity->setResetAutoIncrement(true);
        self::assertTrue($entity->isResetAutoIncrement());
    }

    public function testAddDropDatabase(): void
    {
        $entity = new Database();
        self::assertFalse($entity->isAddDropDatabase());
        $entity->setAddDropDatabase(true);
        self::assertTrue($entity->isAddDropDatabase());
    }

    public function testAddDropTable(): void
    {
        $entity = new Database();
        self::assertFalse($entity->isAddDropTable());
        $entity->setAddDropTable(true);
        self::assertTrue($entity->isAddDropTable());
    }

    public function testAddDropTrigger(): void
    {
        $entity = new Database();
        self::assertTrue($entity->isAddDropTrigger());
        $entity->setAddDropTrigger(false);
        self::assertFalse($entity->isAddDropTrigger());
    }

    public function testAddLocks(): void
    {
        $entity = new Database();
        self::assertTrue($entity->isAddLocks());
        $entity->setAddLocks(false);
        self::assertFalse($entity->isAddLocks());
    }

    public function testCompleteInsert(): void
    {
        $entity = new Database();
        self::assertFalse($entity->isCompleteInsert());
        $entity->setCompleteInsert(true);
        self::assertTrue($entity->isCompleteInsert());
    }

    public function testGetBackupOptions(): void
    {
        $entity = new Database();

        self::assertSame(
            [
                'add-drop-table' => false,
                'add-drop-database' => false,
                'add-drop-trigger' => true,
                'add-locks' => true,
                'complete-insert' => false,
                'reset-auto-increment' => false,
            ],
            $entity->getBackupOptions()
        );

        $entity->setAddDropTable(true)
            ->setAddDropDatabase(true)
            ->setAddDropTrigger(false)
            ->setAddLocks(false)
            ->setCompleteInsert(true)
            ->setResetAutoIncrement(true);

        self::assertSame(
            [
                'add-drop-table' => true,
                'add-drop-database' => true,
                'add-drop-trigger' => false,
                'add-locks' => false,
                'complete-insert' => true,
                'reset-auto-increment' => true,
            ],
            $entity->getBackupOptions()
        );
    }
}
