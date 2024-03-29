<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220801084444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add options for backups';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `database` ADD options_reset_auto_increment TINYINT(1) DEFAULT FALSE NOT NULL, ADD options_add_drop_database TINYINT(1) DEFAULT FALSE NOT NULL, ADD options_add_drop_table TINYINT(1) DEFAULT FALSE NOT NULL, ADD options_add_drop_trigger TINYINT(1) DEFAULT TRUE NOT NULL, ADD options_add_locks TINYINT(1) DEFAULT TRUE NOT NULL, ADD options_complete_insert TINYINT(1) DEFAULT FALSE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `database` DROP options_reset_auto_increment, DROP options_add_drop_database, DROP options_add_drop_table, DROP options_add_drop_trigger, DROP options_add_locks, DROP options_complete_insert');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
