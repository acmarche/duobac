<?php

namespace AcMarche\Duobac\Command;

use AcMarche\Duobac\Repository\UserRepository;
use AcMarche\Duobac\Security\SecurityData;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'duobac:fix',
    description: 'Mise à jour des relevés',
)]
class FixCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->userRepository->findAll() as $user) {
            $user->setRoles([SecurityData::getRoleUser()]);
        }

        $this->userRepository->flush();

        return Command::SUCCESS;
    }
}
