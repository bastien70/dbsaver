<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:post-install',
    description: 'Configure the application after install.',
)]
final class PostInstallCommand extends AbstractDotEnvCommand
{
    public function __construct(private ValidatorInterface $validator, private array $enabledLocales)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $databaseUrl = $io->ask('Database URL (eg. "mysql://root:root@127.0.0.1:3306/dbsaver?serverVersion=5.7")', null, function ($dsn) {
            $this->validateInput($dsn, [new Assert\NotBlank()]);

            return $dsn;
        });
        $mailerDsn = $io->ask('Mailer DSN (eg. "smtp://localhost")', null, function ($dsn) {
            $this->validateInput($dsn, [new Assert\NotBlank()]);

            return $dsn;
        });
        $mailerSender = $io->ask('Mailer sender email (eg. "you@email.com")', null, function ($email) {
            $this->validateInput($email, [new Assert\NotBlank(), new Assert\Email()]);

            return $email;
        });
        $defaultLocale = $io->choice('What locale should be the default one?', $this->enabledLocales);

        $editor = $this->getDotenvEditor($input);
        $editor->set('DATABASE_URL', $databaseUrl);
        $editor->set('MAILER_DSN', $mailerDsn);
        $editor->set('MAILER_SENDER', $mailerSender);
        $editor->set('DEFAULT_LOCALE', $defaultLocale);
        $editor->save();
        $this->removeDotEnvFileIfTest($input);

        $io->success('Parameters have been saved in .env.local file.');

        return Command::SUCCESS;
    }

    private function validateInput(mixed $value, array $constraints): void
    {
        $errors = $this->validator->validate($value, $constraints);

        if (0 < \count($errors)) {
            $combinedErrors = array_map(fn (ConstraintViolationInterface $error) => $error->getMessage(), iterator_to_array($errors));
            throw new \RuntimeException(implode("\n", $combinedErrors));
        }
    }
}
