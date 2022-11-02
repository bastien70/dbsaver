<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221102090002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add a property to enable TOTP.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD totp_enabled TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP totp_enabled');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
