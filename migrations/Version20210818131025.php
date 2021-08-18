<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210818131025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial database structure.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE backup (id INT AUTO_INCREMENT NOT NULL, db_id INT NOT NULL, backup_file_name VARCHAR(255) DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, backup_file_size INT DEFAULT NULL, context VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, original_name VARCHAR(255) DEFAULT NULL, INDEX IDX_3FF0D1ACA2BF053A (db_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `database` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, host VARCHAR(255) NOT NULL, port INT DEFAULT NULL, db_user VARCHAR(255) NOT NULL, db_password VARCHAR(255) NOT NULL, db_name VARCHAR(255) NOT NULL, max_backups INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_C953062EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE backup ADD CONSTRAINT FK_3FF0D1ACA2BF053A FOREIGN KEY (db_id) REFERENCES `database` (id)');
        $this->addSql('ALTER TABLE `database` ADD CONSTRAINT FK_C953062EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE backup DROP FOREIGN KEY FK_3FF0D1ACA2BF053A');
        $this->addSql('ALTER TABLE `database` DROP FOREIGN KEY FK_C953062EA76ED395');
        $this->addSql('DROP TABLE backup');
        $this->addSql('DROP TABLE `database`');
        $this->addSql('DROP TABLE user');
    }
}
