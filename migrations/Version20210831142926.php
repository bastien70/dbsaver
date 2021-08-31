<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20210831142926 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return 'Add locale on User.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD locale VARCHAR(2) NOT NULL');
        $this->addSql('UPDATE user SET locale = :locale;', ['locale' => $this->container->getParameter('kernel.default_locale')]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP locale');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
