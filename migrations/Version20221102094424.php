<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221102094424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '2FA: handle backup codes.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD backup_codes LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('UPDATE user SET backup_codes = "[]";');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP backup_codes');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
