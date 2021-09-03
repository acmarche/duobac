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

    private ImportManager $importManager;
    private ParameterBagInterface $parameterBag;
    private array $types = ['duobac', 'moyenne'];
    private MoyenneManager $moyenneManager;
    private PeseeRepository $peseeRepository;

    public function __construct(
        ImportManager $importManager,
        MoyenneManager $moyenneManager,
        PeseeRepository $peseeRepository,
        ParameterBagInterface $parameterBag
    ) {
        parent::__construct();
        $this->importManager = $importManager;
        $this->parameterBag = $parameterBag;
        $this->moyenneManager = $moyenneManager;
        $this->peseeRepository = $peseeRepository;
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
        $year = (int)$input->getArgument('annee');
        $type = $input->getArgument('type');

        if (!in_array($type, $this->types)) {
            $io->error('Erreur pour le type, les choix possibles sont: '.implode(',', $this->types));

            return Command::FAILURE;
        }

        if (!$year) {
            return Command::FAILURE;
        }

        $file = $this->parameterBag->get('kernel.project_dir').'/data/Pesees2021.csv';

        if ($type === 'duobac') {
            $i = 0;
            $this->peseeRepository->removeByYear($year);
            foreach ($this->importManager->getLines($file) as $data) {
                $io->writeln($data[1].' '.$i);
                $this->importManager->treatment($data, $year);
                $i++;
            }
        }

        if ($type === 'moyenne') {
            $output->writeln($year);
            $this->moyenneManager->deleteByYear($year);
            $this->moyenneManager->setIo($io);
            $this->moyenneManager->execute($year);
        }

        return Command::SUCCESS;
    }
}
