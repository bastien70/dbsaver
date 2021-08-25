<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Backup;
use App\Service\BackupService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:backup',
    description: 'Make a database backup',
)]
final class BackupCommand extends Command
{
    public function __construct(
        private BackupService $backupService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $databases = $this->backupService->getDatabases();
            $databasesCount = \count($databases);

            if ($databasesCount > 0) {
                $io->section('Starting backups');
                $io->progressStart($databasesCount);

                foreach ($databases as $database) {
                    $this->backupService->backup($database, Backup::CONTEXT_AUTOMATIC);
                    $io->progressAdvance();
                }

                $io->progressFinish();

                $io->section('Cleaning old backups');
                $io->progressStart($databasesCount);

                foreach ($databases as $database) {
                    $this->backupService->clean($database);
                    $io->progressAdvance();
                }

                $io->progressFinish();
            }
        } catch (\Exception $e) {
            dump($e->getMessage());

            return Command::INVALID;
        }

        return Command::SUCCESS;
    }
}
