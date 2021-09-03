<?php

declare(strict_types=1);

namespace App\Command;

use sixlive\DotenvEditor\DotenvEditor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
    private SymfonyStyle $io;
    private DotenvEditor $dotenvEditor;
    private bool $onlyMissing;
    private bool $anyValueWasUpdated = false;

    public function __construct(private ValidatorInterface $validator, private array $enabledLocales)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('only-missing', 'm', InputOption::VALUE_NONE, 'Only configure non existent parameters');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->dotenvEditor = $this->getDotenvEditor($input);
        $this->onlyMissing = $input->getOption('only-missing');

        $this->ask('DATABASE_URL', 'Dztabase URL (eg. "mysql://root:root@127.0.0.1:3306/dbsaver?serverVersion=mariadb-10.6.2")', function ($dsn) {
            $this->validateInput($dsn, [new Assert\NotBlank()]);

            return $dsn;
        });
        $this->ask('MAILER_DSN', 'Mailer DSN (eg. "smtp://localhost")', function ($dsn) {
            $this->validateInput($dsn, [new Assert\NotBlank()]);

            return $dsn;
        });
        $this->ask('MAILER_SENDER', 'Mailer sender email (eg. "you@email.com")', function ($email) {
            $this->validateInput($email, [new Assert\NotBlank(), new Assert\Email()]);

            return $email;
        });
        $this->choice('DEFAULT_LOCALE', 'What locale should be the default one?', $this->enabledLocales);

        $this->dotenvEditor->save();
        $this->removeDotEnvFileIfTest($input);

        if ($this->anyValueWasUpdated) {
            $this->io->success('Parameters have been saved in .env.local file.');
        } else {
            $this->io->success('No parameters were missing.');
        }

        return Command::SUCCESS;
    }

    private function ask(string $key, string $question, callable $callback): void
    {
        $value = $this->getValueFromDotEnv($this->dotenvEditor, $key);
        if (null === $value || !$this->onlyMissing) {
            $value = $this->io->ask($question, $value, $callback);
            $this->dotenvEditor->set($key, $value);
            $this->anyValueWasUpdated = true;
        }
    }

    private function choice(string $key, string $question, array $choices): void
    {
        $value = $this->getValueFromDotEnv($this->dotenvEditor, $key);
        if (null === $value || !$this->onlyMissing) {
            $value = $this->io->choice(
                $question,
                $choices,
                $value,
            );
            $this->dotenvEditor->set($key, $value);
            $this->anyValueWasUpdated = true;
        }
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
