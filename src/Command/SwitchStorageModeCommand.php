<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\YamlService;
use RuntimeException;
use sixlive\DotenvEditor\DotenvEditor;
use function sprintf;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:switch-storage-mode',
    description: 'Switch storage mode',
)]
final class SwitchStorageModeCommand extends AbstractDotEnvCommand
{
    public const FILE_DIR = 'config/packages';
    public const FILE_NAME = 'vich_uploader';

    public const STORAGE_LOCALLY = 'Locally';
    public const STORAGE_AWS = 'AWS S3';

    private YamlService $yamlService;
    private ?DotenvEditor $editor = null;
    private ?SymfonyStyle $io = null;

    public function __construct(string $projectDir)
    {
        parent::__construct();
        $this->yamlService = new YamlService(self::FILE_DIR, self::FILE_NAME, $projectDir);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->editor = $this->getDotenvEditor($input);

        $storageChoice = (string) $this->io->choice(
            'Where do you want to store backups?',
            [self::STORAGE_LOCALLY, self::STORAGE_AWS],
            self::STORAGE_LOCALLY
        );

        switch ($storageChoice) {
            case self::STORAGE_LOCALLY:
                $this->switchToLocally();
                break;

            case self::STORAGE_AWS:
                $this->switchToAws();
                break;
        }

        $this->editor->save();
        $this->removeDotEnvFileIfTest($input);

        $this->io->success(sprintf('Successfully switched to %s storage!', $storageChoice));

        return Command::SUCCESS;
    }

    private function switchToLocally(): void
    {
        $this->editor->set('BACKUP_LOCAL', '1');

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

    private function switchToAws(): void
    {
        $this->updateEnvVariables('AWS_S3_ACCESS_ID', 'Your AWS S3 access ID');
        $this->updateEnvVariables('AWS_S3_ACCESS_SECRET', 'Your AWS S3 access secret');
        $this->updateEnvVariables('AWS_S3_BUCKET_NAME', 'The AWS S3 bucket name');
        $this->updateEnvVariables('AWS_S3_REGION', 'The AWS S3 region', 'eu-west-3');

        $this->editor->set('BACKUP_LOCAL', '0');

        $content = [
            'vich_uploader' => [
                'db_driver' => 'orm',
                'storage' => 'gaufrette',
                'mappings' => [
                    'backups' => [
                        'uri_prefix' => '%uploads_base_url%',
                        'upload_destination' => 'backup_fs',
                    ],
                ],
            ],
        ];

        $this->yamlService->writeFileContent($content);
    }

    private function updateEnvVariables(string $key, string $question, ?string $default = null): void
    {
        if (!$default) {
            $default = $this->editor->has($key) ? (string) $this->editor->getEnv($key) : null;
        }

        $this->io->ask(
            $question,
            $default,
            function ($value) use ($key) {
                if (empty($value)) {
                    throw new RuntimeException('This value should not be blank.');
                }
                $this->editor->set($key, (string) $value);
            }
        );
    }
}
