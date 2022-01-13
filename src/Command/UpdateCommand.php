<?php

namespace AcMarche\Duobac\Command;

use AcMarche\Duobac\Import\ImportManager;
use AcMarche\Duobac\Import\MoyenneManager;
use AcMarche\Duobac\Repository\PeseeRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UpdateCommand extends Command
{
    protected static $defaultName = 'duobac:update';
    private array $types = ['duobac', 'moyenne'];

    public function __construct(
        private ImportManager $importManager,
        private MoyenneManager $moyenneManager,
        private PeseeRepository $peseeRepository,
        private ParameterBagInterface $parameterBag
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Mise à jour des releves')
            ->addArgument('annee', InputArgument::REQUIRED, 'Indiquée l\'année')
            ->addArgument('type', InputArgument::REQUIRED, 'Choix:'.implode(',', $this->types));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $year = (int) $input->getArgument('annee');
        $type = $input->getArgument('type');

        if (!\in_array($type, $this->types)) {
            $io->error('Erreur pour le type, les choix possibles sont: '.implode(',', $this->types));

            return Command::FAILURE;
        }

        if (0 === $year) {
            return Command::FAILURE;
        }

        $file = $this->parameterBag->get('kernel.project_dir').'/data/Pesees2021.csv';

        if ('duobac' === $type) {
            $i = 0;
            $this->peseeRepository->removeByYear($year);
            foreach ($this->importManager->getLines($file) as $data) {
                $io->writeln($data[1].' '.$i);
                $this->importManager->treatment($data, $year);
                ++$i;
            }
        }

        if ('moyenne' === $type) {
            $output->writeln($year);
            $this->moyenneManager->deleteByYear($year);
            $this->moyenneManager->setIo($io);
            $this->moyenneManager->execute($year);
        }

        return Command::SUCCESS;
    }
}
