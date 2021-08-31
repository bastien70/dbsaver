<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210824073653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE backup DROP FOREIGN KEY FK_3FF0D1ACF0AA09DB');
        $this->addSql('ALTER TABLE backup ADD CONSTRAINT FK_3FF0D1ACF0AA09DB FOREIGN KEY (database_id) REFERENCES `database` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `database` DROP FOREIGN KEY FK_C953062E7E3C61F9');
        $this->addSql('ALTER TABLE `database` ADD CONSTRAINT FK_C953062E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE backup DROP FOREIGN KEY FK_3FF0D1ACF0AA09DB');
        $this->addSql('ALTER TABLE backup ADD CONSTRAINT FK_3FF0D1ACF0AA09DB FOREIGN KEY (database_id) REFERENCES `database` (id)');
        $this->addSql('ALTER TABLE `database` DROP FOREIGN KEY FK_C953062E7E3C61F9');
        $this->addSql('ALTER TABLE `database` ADD CONSTRAINT FK_C953062E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
