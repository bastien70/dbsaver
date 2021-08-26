<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:regenerate-app-secret',
    description: 'Regenerate APP_SECRET',
)]
final class RegenerateAppSecretCommand extends AbstractDotEnvCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $a = '0123456789abcdef';
        $secret = '';
        for ($i = 0; $i < 32; ++$i) {
            $secret .= $a[random_int(0, 15)];
        }

        $editor = $this->getDotenvEditor($input);
        $editor->set('APP_SECRET', $secret);
        $editor->save();
        $this->removeDotEnvFileIfTest($input);

        $io->success('The new APP_SECRET env var has been regenerated!');

        return Command::SUCCESS;
    }
}
