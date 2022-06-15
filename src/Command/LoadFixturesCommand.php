<?php

declare(strict_types=1);

namespace App\Command;

use App\Factory\BackupFactory;
use App\Factory\DatabaseFactory;
use App\Factory\LocalAdapterFactory;
use App\Factory\S3AdapterFactory;
use App\Factory\UserFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\When;
use Zenstruck\Foundry\Factory;

#[AsCommand('app:fixtures:load')]
#[When('dev')]
#[When('test')]
final class LoadFixturesCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = UserFactory::createOne(['email' => 'user@test.com']);
        $admin = UserFactory::new(['email' => 'admin@test.com'])->asAdmin()->create();
        $localAdapter = LocalAdapterFactory::createOne();
        $s3Adapter = S3AdapterFactory::createOne();

        Factory::delayFlush(static function () use ($user, $admin, $localAdapter, $s3Adapter): void {
            DatabaseFactory::new()->withOwner($user)->withAdapter($localAdapter)->create();
            DatabaseFactory::new()->withOwner($user)->withAdapter($s3Adapter)->create();
            DatabaseFactory::new()->withOwner($admin)->withAdapter($localAdapter)->create();
            DatabaseFactory::new()->withOwner($admin)->withAdapter($s3Adapter)->create();
        });

        Factory::delayFlush(static function (): void {
            BackupFactory::createMany(5);
        });

        Factory::delayFlush(static function () use ($admin, $localAdapter, $s3Adapter): void {
            DatabaseFactory::new()->withOwner($admin)->withAdapter($localAdapter)->withBadPassword()->create();
            DatabaseFactory::new()->withOwner($admin)->withAdapter($s3Adapter)->withBadPassword()->create();
        });

        return Command::SUCCESS;
    }
}
