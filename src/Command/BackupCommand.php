<?php

namespace App\Command;

use App\Service\BackupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:backup')]
class BackupCommand extends Command
{
    /**
     * @throws \Exception
     */
    public function __construct(
        string $name = null,
        private BackupService $backupService,
        private EntityManagerInterface $manager
    )
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Make a database backup')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $databases = $this->backupService->getDatabases();
            $databasesCount = count($databases);

            if($databasesCount > 0)
            {
                $io->section('Démarrage des backups');
                $io->progressStart($databasesCount);

                foreach($databases as $database)
                {
                    $this->backupService->backup($database, 'Backup quotidien');
                    $io->progressAdvance();
                }

                $io->progressFinish();

                $io->section('Nettoyage des anciennes sauvegardes');
                $io->progressStart($databasesCount);

                foreach($databases as $database)
                {
                    $this->backupService->clean($database);
                    $io->progressAdvance();
                }

                $io->progressFinish();
            }

        } catch (\Exception $e)
        {
            dump($e->getMessage());
            return Command::INVALID;
        }

        return Command::SUCCESS;
    }
}