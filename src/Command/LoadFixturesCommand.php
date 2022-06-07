<?php

declare(strict_types=1);

namespace App\Command;

use App\Factory\BackupFactory;
use App\Factory\DatabaseFactory;
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

        Factory::delayFlush(static function () use ($user, $admin): void {
            DatabaseFactory::new()->withOwner($user)->many(5)->create();
            DatabaseFactory::new()->withOwner($admin)->many(5)->create();
        });

        Factory::delayFlush(static function (): void {
            BackupFactory::createMany(30);
        });

        return Command::SUCCESS;
    }
}
