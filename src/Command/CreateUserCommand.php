<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\UserProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user account',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $newUserProfile = new UserProfile();

        $newUser = new User();
        $newUser->setEmail($email);
        $newUser->setPassword(
            $this->userPasswordHasher->hashPassword(
                $newUser,
                $password
            )
        );
        $newUser->setUserProfile($newUserProfile);
        $this->entityManager->persist($newUser);
        $this->entityManager->flush();

        $io->success(sprintf('User %s account was created.', $email));

        return Command::SUCCESS;
    }
}
