<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210820134234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename some fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE backup DROP FOREIGN KEY FK_3FF0D1ACA2BF053A');
        $this->addSql('DROP INDEX IDX_3FF0D1ACA2BF053A ON backup');
        $this->addSql('ALTER TABLE backup CHANGE db_id database_id INT NOT NULL');
        $this->addSql('ALTER TABLE backup ADD CONSTRAINT FK_3FF0D1ACF0AA09DB FOREIGN KEY (database_id) REFERENCES `database` (id)');
        $this->addSql('CREATE INDEX IDX_3FF0D1ACF0AA09DB ON backup (database_id)');
        $this->addSql('ALTER TABLE `database` DROP FOREIGN KEY FK_C953062EA76ED395');
        $this->addSql('DROP INDEX IDX_C953062EA76ED395 ON `database`');
        $this->addSql('ALTER TABLE `database` CHANGE db_user user VARCHAR(255) NOT NULL, CHANGE db_password password VARCHAR(255) NOT NULL, CHANGE db_name name VARCHAR(255) NOT NULL, CHANGE user_id owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE `database` ADD CONSTRAINT FK_C953062E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C953062E7E3C61F9 ON `database` (owner_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE backup DROP FOREIGN KEY FK_3FF0D1ACF0AA09DB');
        $this->addSql('DROP INDEX IDX_3FF0D1ACF0AA09DB ON backup');
        $this->addSql('ALTER TABLE backup CHANGE database_id db_id INT NOT NULL');
        $this->addSql('ALTER TABLE backup ADD CONSTRAINT FK_3FF0D1ACA2BF053A FOREIGN KEY (db_id) REFERENCES `database` (id)');
        $this->addSql('CREATE INDEX IDX_3FF0D1ACA2BF053A ON backup (db_id)');
        $this->addSql('ALTER TABLE `database` DROP FOREIGN KEY FK_C953062E7E3C61F9');
        $this->addSql('DROP INDEX IDX_C953062E7E3C61F9 ON `database`');
        $this->addSql('ALTER TABLE `database` ADD db_user VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD db_password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD db_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP user, DROP password, DROP name, CHANGE owner_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE `database` ADD CONSTRAINT FK_C953062EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C953062EA76ED395 ON `database` (user_id)');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
