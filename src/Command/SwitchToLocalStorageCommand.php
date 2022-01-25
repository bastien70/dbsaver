<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\YamlService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:switch-local-storage',
    description: 'Switch to local storage',
)]
final class SwitchToLocalStorageCommand extends AbstractDotEnvCommand
{
    public const FILE_DIR = 'config/packages';
    public const FILE_NAME = 'vich_uploader';
    private YamlService $yamlService;

    public function __construct()
    {
        parent::__construct();
        $this->yamlService = new YamlService(self::FILE_DIR, self::FILE_NAME);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $editor = $this->getDotenvEditor($input);
        $editor->set('BACKUP_LOCAL', '0');
        $editor->save();
        $this->removeDotEnvFileIfTest($input);

        $this->updateConfigFile();

        $io->success('Successfully switched to local storage!');

        return Command::SUCCESS;
    }

    private function updateConfigFile(): void
    {
        $content = [
            'vich_uploader' => [
                'db_driver' => 'orm',
                'mappings' => [
                    'backups' => [
                        'uri_prefix' => '/files/backups',
                        'upload_destination' => '%kernel.project_dir%/public/files/backups',
                    ],
                ],
            ],
        ];

        $this->yamlService->writeFileContent($content);
    }
}
