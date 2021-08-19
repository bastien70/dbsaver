<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210819074722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'User refactor: one role per user. Defaults existing users to admin role.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD role VARCHAR(255) NOT NULL, DROP roles');
        $this->addSql('UPDATE user SET role = "ROLE_ADMIN";');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', DROP role');
    }
}
