<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230805105031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added FTP support';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adapter_config ADD ftp_host VARCHAR(255) DEFAULT NULL, ADD ftp_username VARCHAR(255) DEFAULT NULL, ADD ftp_password VARCHAR(255) DEFAULT NULL, ADD ftp_port INT DEFAULT NULL, ADD ftp_ssl TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adapter_config DROP ftp_host, DROP ftp_username, DROP ftp_password, DROP ftp_port, DROP ftp_ssl');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
