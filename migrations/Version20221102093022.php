<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221102093022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '2FA: handle trusted devices.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD trusted_token_version INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP trusted_token_version');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
