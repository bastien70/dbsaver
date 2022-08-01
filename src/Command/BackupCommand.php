<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Backup;
use App\Entity\Database;
use App\Repository\DatabaseRepository;
use App\Service\BackupService;
use App\Service\BackupStatus;
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
        private readonly BackupService $backupService,
        private readonly DatabaseRepository $databaseRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $databases = $this->backupService->getDatabasesToBackup();
        $databasesCount = \count($databases);

        if ($databasesCount > 0) {
            $errors = [];
            $io->section('Starting backups');
            $io->progressStart($databasesCount);

            foreach ($databases as $database) {
                $backupStatus = $this->backupService->backup($database, Backup::CONTEXT_AUTOMATIC);
                if (BackupStatus::STATUS_OK === $backupStatus->getStatus()) {
                    $database->setStatus(Database::STATUS_OK);
                    $backupTask = $database->getBackupTask();
                    $backupTask->setNextIteration($backupTask->calculateNextIteration());
                } else {
                    $database->setStatus(Database::STATUS_ERROR);
                    $errors[] = [
                        'database' => $database,
                        'message' => $backupStatus->getErrorMessage(),
                    ];
                }
                $this->databaseRepository->save($database);
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

            if (0 < \count($errors)) {
                $io->error([\count($errors) . ' errors happened'] + array_map(static function (array $error): string {
                    return $error['database']->getName() . ': ' . $error['message'];
                }, $errors));

                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
