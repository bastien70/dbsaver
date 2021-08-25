<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Database;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210825120223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add connection status on databases.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `database` ADD status VARCHAR(10) NOT NULL');
        $this->addSql('UPDATE `database` SET status = :unknown;', ['unknown' => Database::STATUS_UNKNOWN]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `database` DROP status');
    }
}
