<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Database;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testId(): void
    {
        $entity = new User();
        self::assertNull($entity->getId());
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, 15);
        self::assertSame(15, $entity->getId());
    }

    public function testToString(): void
    {
        $entity = new User();
        $entity->setEmail('user@test.com');
        self::assertSame('user@test.com', (string) $entity);
    }

    public function testEmail(): void
    {
        $entity = new User();
        $entity->setEmail('user@test.com');
        self::assertSame('user@test.com', $entity->getEmail());
    }

    public function testUserIdentifier(): void
    {
        $entity = new User();
        $entity->setEmail('user@test.com');
        self::assertSame('user@test.com', $entity->getUserIdentifier());
    }

    public function testUsername(): void
    {
        $entity = new User();
        $entity->setEmail('user@test.com');
        self::assertSame('user@test.com', $entity->getUsername());
    }

    public function testRole(): void
    {
        $entity = new User();
        $entity->setRole(User::ROLE_ADMIN);
        self::assertSame(User::ROLE_ADMIN, $entity->getRole());
        self::assertSame([User::ROLE_ADMIN], $entity->getRoles());
    }

    public function testPassword(): void
    {
        $entity = new User();
        $entity->setPassword('redacted');
        self::assertSame('redacted', $entity->getPassword());
    }

    public function testPlainPassword(): void
    {
        $entity = new User();
        self::assertNull($entity->getPlainPassword());
        $entity->setPlainPassword('plain');
        self::assertSame('plain', $entity->getPlainPassword());
        $entity->eraseCredentials();
        self::assertNull($entity->getPlainPassword());
    }

    public function testDatabases(): void
    {
        $entity = new User();
        $database = new Database();
        self::assertCount(0, $entity->getDatabases());
        self::assertNull($database->getOwner());

        $entity->addDatabase($database);
        self::assertCount(1, $entity->getDatabases());
        self::assertSame($database, $entity->getDatabases()[0]);
        self::assertSame($entity, $database->getOwner());

        $entity->removeDatabase($database);
        self::assertCount(0, $entity->getDatabases());
        self::assertNull($database->getOwner());
    }
}
