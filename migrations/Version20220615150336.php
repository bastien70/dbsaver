<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220615150336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow user to create storage space from dashboard and select it for each database';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE adapter_config (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, prefix VARCHAR(255) DEFAULT NULL, adapter VARCHAR(255) NOT NULL, s3_access_id VARCHAR(255) DEFAULT NULL, s3_access_secret VARCHAR(255) DEFAULT NULL, s3_bucket_name VARCHAR(255) DEFAULT NULL, s3_region VARCHAR(255) DEFAULT NULL, s3_provider VARCHAR(255) DEFAULT NULL, storage_class VARCHAR(255) DEFAULT NULL, s3_endpoint VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE backup DROP original_name');
        $this->addSql('ALTER TABLE `database` ADD adapter_id INT NOT NULL');
        $this->addSql('ALTER TABLE `database` ADD CONSTRAINT FK_C953062EB55E6441 FOREIGN KEY (adapter_id) REFERENCES adapter_config (id)');
        $this->addSql('CREATE INDEX IDX_C953062EB55E6441 ON `database` (adapter_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `database` DROP FOREIGN KEY FK_C953062EB55E6441');
        $this->addSql('DROP TABLE adapter_config');
        $this->addSql('ALTER TABLE backup ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_C953062EB55E6441 ON `database`');
        $this->addSql('ALTER TABLE `database` DROP adapter_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
