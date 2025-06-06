<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
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
    name: 'app:create-admin-user',
    description: 'Creates a new admin user',
)]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Admin user email')
            ->addArgument('password', InputArgument::REQUIRED, 'Admin user password')
            ->addArgument('name', InputArgument::OPTIONAL, 'Admin name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $name = $input->getArgument('name') ?: 'Admin User';
        
        // Check if user already exists
        $existingUser = $this->userRepository->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->error(sprintf('User with email %s already exists', $email));
            return Command::FAILURE;
        }
        
        // Create new admin user
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        
        // Hash the password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        
        // Save user to database
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        $io->success(sprintf('Admin user created: %s with email %s', $name, $email));
        
        return Command::SUCCESS;
    }
}
