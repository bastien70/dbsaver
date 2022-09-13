<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220902073138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change database options';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `database` CHANGE options_reset_auto_increment options_reset_auto_increment TINYINT(1) NOT NULL, CHANGE options_add_drop_database options_add_drop_database TINYINT(1) NOT NULL, CHANGE options_add_drop_table options_add_drop_table TINYINT(1) NOT NULL, CHANGE options_add_drop_trigger options_add_drop_trigger TINYINT(1) NOT NULL, CHANGE options_add_locks options_add_locks TINYINT(1) NOT NULL, CHANGE options_complete_insert options_complete_insert TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `database` CHANGE options_reset_auto_increment options_reset_auto_increment TINYINT(1) DEFAULT 0 NOT NULL, CHANGE options_add_drop_database options_add_drop_database TINYINT(1) DEFAULT 0 NOT NULL, CHANGE options_add_drop_table options_add_drop_table TINYINT(1) DEFAULT 0 NOT NULL, CHANGE options_add_drop_trigger options_add_drop_trigger TINYINT(1) DEFAULT 1 NOT NULL, CHANGE options_add_locks options_add_locks TINYINT(1) DEFAULT 1 NOT NULL, CHANGE options_complete_insert options_complete_insert TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
