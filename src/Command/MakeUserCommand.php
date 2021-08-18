<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:make-user',
    description: 'Create user',
)]
final class MakeUserCommand extends Command
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface $manager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $io->ask('Email address');
        $plainPassword = $io->askHidden('Password');

        $user = new User();
        $user->setEmail($email)
            ->setPassword($this->hasher->hashPassword($user, $plainPassword));

        $this->manager->persist($user);
        $this->manager->flush();

        $io->success(
            sprintf('User %s has been successfully created! You can now log in.', $email)
        );

        return Command::SUCCESS;
    }
}
