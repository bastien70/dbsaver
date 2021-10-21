<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:make-user',
    description: 'Create user',
)]
final class MakeUserCommand extends Command
{
    /**
     * @param array<string> $enabledLocales
     */
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface $manager,
        private ValidatorInterface $validator,
        private array $enabledLocales,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $io->ask('Email address', null, function ($email): string {
            $errors = $this->validator->validate($email, [new Assert\NotBlank(), new Assert\Email()]);

            if (0 < \count($errors)) {
                $combinedErrors = array_map(fn (ConstraintViolationInterface $error) => $error->getMessage(), iterator_to_array($errors));
                throw new \RuntimeException(implode("\n", $combinedErrors));
            }

            return $email;
        });
        $plainPassword = $io->askHidden('Password', function ($password): string {
            $errors = $this->validator->validate($password, [new Assert\NotBlank()]);

            if (0 < \count($errors)) {
                $combinedErrors = array_map(fn (ConstraintViolationInterface $error) => $error->getMessage(), iterator_to_array($errors));
                throw new \RuntimeException(implode("\n", $combinedErrors));
            }

            return $password;
        });
        $locale = $io->choice('Locale', $this->enabledLocales);
        $receiveAutomaticEmails = $io->confirm('Should the user receive automatic emails?');
        $role = $io->choice('Role', User::getAvailableRoles(), 'ROLE_USER');

        $user = new User();
        $user->setEmail($email)
            ->setPassword($this->hasher->hashPassword($user, $plainPassword))
            ->setLocale($locale)
            ->setReceiveAutomaticEmails($receiveAutomaticEmails)
            ->setRole($role);

        try {
            $this->manager->persist($user);
            $this->manager->flush();
        } catch (UniqueConstraintViolationException) {
            $io->error(sprintf('Email address %s is already used.', $email));

            return Command::FAILURE;
        }

        $io->success(
            sprintf('User %s has been successfully created! You can now log in.', $email)
        );

        return Command::SUCCESS;
    }
}
