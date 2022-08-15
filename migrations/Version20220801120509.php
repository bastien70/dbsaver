<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Enum\BackupTaskPeriodicity;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220801120509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added backup custom periodicity support';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `database` ADD backup_task_periodicity VARCHAR(255) NOT NULL, ADD backup_task_periodicity_number INT NOT NULL, ADD backup_task_start_from DATETIME NOT NULL, ADD backup_task_next_iteration DATETIME NOT NULL');
        $this->addSql('UPDATE `database` SET backup_task_periodicity = :periodicity, backup_task_periodicity_number = :periodicity_number, backup_task_start_from = :start_from, backup_task_next_iteration = :next_iteration;', [
            'periodicity_number' => 1,
            'periodicity' => BackupTaskPeriodicity::WEEK->value,
            'start_from' => (new \DateTime())->format('Y-m-d'),
            'next_iteration' => (new \DateTime('+1 week'))->format('Y-m-d'),
        ]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `database` DROP backup_task_periodicity, DROP backup_task_periodicity_number, DROP backup_task_start_from, DROP backup_task_next_iteration');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
