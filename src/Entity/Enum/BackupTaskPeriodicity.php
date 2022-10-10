<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum BackupTaskPeriodicity: string
{
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case YEAR = 'year';

    public function formLabel(): string
    {
        return match ($this) {
            self::DAY => 'enum.backup_task_periodicity.select.day',
            self::WEEK => 'enum.backup_task_periodicity.select.week',
            self::MONTH => 'enum.backup_task_periodicity.select.month',
            self::YEAR => 'enum.backup_task_periodicity.select.year',
        };
    }
}
