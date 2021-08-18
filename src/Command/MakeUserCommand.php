<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:make-user',
    description: 'Create user',
)]
class MakeUserCommand extends Command
{
    public function __construct(
        string $name = null,
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface $manager,
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $io->ask('Adresse email');
        $plainPassword = $io->askHidden('Mot de passe');

        $user = new User();
        $user->setEmail($email)
            ->setPassword($this->hasher->hashPassword($user, $plainPassword));

        $this->manager->persist($user);
        $this->manager->flush();

        $io->success(
            sprintf("L'utilisateur %s a bien été créé ! Vous pouvez désormais vous connecter", $email)
        );

        return Command::SUCCESS;
    }
}
