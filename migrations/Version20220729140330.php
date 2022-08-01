<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220729140330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add options for backups';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `database` ADD reset_auto_increment TINYINT(1) DEFAULT FALSE NOT NULL, ADD add_drop_database TINYINT(1) DEFAULT FALSE NOT NULL, ADD add_drop_table TINYINT(1) DEFAULT FALSE NOT NULL, ADD add_drop_trigger TINYINT(1) DEFAULT TRUE NOT NULL, ADD add_locks TINYINT(1) DEFAULT TRUE NOT NULL, ADD complete_insert TINYINT(1) DEFAULT FALSE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `database` DROP reset_auto_increment, DROP add_drop_database, DROP add_drop_table, DROP add_drop_trigger, DROP add_locks, DROP complete_insert');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
