<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\YamlService;
use RuntimeException;
use sixlive\DotenvEditor\DotenvEditor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:switch-aws-storage',
    description: 'Switch to AWS S3 storage',
)]
final class SwitchToAwsStorageCommand extends AbstractDotEnvCommand
{
    public const FILE_DIR = 'config/packages';
    public const FILE_NAME = 'vich_uploader';
    private YamlService $yamlService;
    private ?DotenvEditor $editor = null;
    private ?SymfonyStyle $io = null;

    public function __construct()
    {
        parent::__construct();
        $this->yamlService = new YamlService(self::FILE_DIR, self::FILE_NAME);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->editor = $this->getDotenvEditor($input);

        $this->updateAwsEnvVariables('AWS_S3_ACCESS_ID', 'Your AWS S3 access ID');
        $this->updateAwsEnvVariables('AWS_S3_ACCESS_SECRET', 'Your AWS S3 access secret');
        $this->updateAwsEnvVariables('AWS_S3_BUCKET_NAME', 'The AWS S3 bucket name');
        $this->updateAwsEnvVariables('AWS_S3_REGION', 'The AWS S3 region', 'eu-west-3');

        $this->editor->set('BACKUP_LOCAL', '1');
        $this->editor->save();
        $this->removeDotEnvFileIfTest($input);

        $this->updateConfigFile();

        $this->io->success('Successfully switched to AWS S3 storage!');

        return Command::SUCCESS;
    }

    private function updateAwsEnvVariables(string $key, string $question, ?string $default = null): void
    {
        $editor = $this->editor;

        if (!$default) {
            $default = $editor->has($key) ? (string) $editor->getEnv($key) : null;
        }

        $this->io->ask(
            $question,
            $default,
            function ($value) use ($editor, $key) {
                if (empty($value)) {
                    throw new RuntimeException('This value should not be blank.');
                }
                $editor->set($key, (string) $value);
            }
        );
    }

    private function updateConfigFile(): void
    {
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
}
