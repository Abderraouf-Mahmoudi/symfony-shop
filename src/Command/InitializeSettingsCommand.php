<?php

namespace App\Command;

use App\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:initialize-settings',
    description: 'Initialize default settings in the database',
)]
class InitializeSettingsCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $settingRepository = $this->entityManager->getRepository(Setting::class);
        
        // Check if income setting exists
        $incomeSetting = $settingRepository->findOneBy(['name' => 'income']);
        
        if (!$incomeSetting) {
            $incomeSetting = new Setting();
            $incomeSetting->setName('income');
            $incomeSetting->setValue('0');
            
            $this->entityManager->persist($incomeSetting);
            $this->entityManager->flush();
            
            $io->success('Income setting initialized successfully');
        } else {
            $io->info('Income setting already exists');
        }
        
        return Command::SUCCESS;
    }
}
