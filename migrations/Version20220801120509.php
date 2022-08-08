<?php

declare(strict_types=1);

namespace DoctrineMigrations;

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
