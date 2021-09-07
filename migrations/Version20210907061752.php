<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210907061752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow user to choose whether or not to receive automatic emails.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD receive_automatic_emails TINYINT(1) NOT NULL');
        $this->addSql('UPDATE user SET receive_automatic_emails = 1;'); // Not to break previous behaviour
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP receive_automatic_emails');
    }
}
