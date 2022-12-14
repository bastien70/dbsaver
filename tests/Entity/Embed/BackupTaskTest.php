<?php

declare(strict_types=1);

namespace App\Tests\Entity\Embed;

use App\Entity\Embed\BackupTask;
use App\Entity\Enum\BackupTaskPeriodicity;
use PHPUnit\Framework\TestCase;

class BackupTaskTest extends TestCase
{
    public function testPeriodicity(): void
    {
        $entity = new BackupTask();
        self::assertNull($entity->getPeriodicity());
        $entity->setPeriodicity(BackupTaskPeriodicity::WEEK);
        self::assertSame(BackupTaskPeriodicity::WEEK, $entity->getPeriodicity());
    }

    public function testPeriodicityNumber(): void
    {
        $entity = new BackupTask();
        self::assertNull($entity->getPeriodicityNumber());
        $entity->setPeriodicityNumber(1);
        self::assertSame(1, $entity->getPeriodicityNumber());
    }

    public function testStartFrom(): void
    {
        $entity = new BackupTask();
        self::assertNull($entity->getStartFrom());
        $tomorrow = new \DateTime('tomorrow');
        $entity->setStartFrom($tomorrow);
        self::assertSame($tomorrow, $entity->getStartFrom());
    }

    public function testNextIteration(): void
    {
        $entity = new BackupTask();
        self::assertNull($entity->getNextIteration());
        $tomorrow = new \DateTime('tomorrow');
        $entity->setNextIteration($tomorrow);
        self::assertSame($tomorrow, $entity->getNextIteration());
    }

    public function testCalculateNextIteration(): void
    {
        $entity = new BackupTask();
        $entity->setPeriodicity(BackupTaskPeriodicity::DAY)
            ->setPeriodicityNumber(1)
            ->setNextIteration(new \DateTimeImmutable());

        $nextIteration = $entity->calculateNextIteration();

        self::assertGreaterThan(
            $entity->getNextIteration()->getTimestamp(),
            $nextIteration->getTimestamp()
        );
    }
}
