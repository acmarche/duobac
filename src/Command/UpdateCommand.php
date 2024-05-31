<?php

namespace AcMarche\Duobac\Command;

use AcMarche\Duobac\Import\ImportManager;
use AcMarche\Duobac\Import\MoyenneManager;
use AcMarche\Duobac\Repository\PeseeRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'duobac:update',
    description: 'Mise à jour des relevés',
)]
class UpdateCommand extends Command
{
    private array $types = ['duobac', 'moyenne'];

    public function __construct(
        private readonly ImportManager $importManager,
        private readonly MoyenneManager $moyenneManager,
        private readonly PeseeRepository $peseeRepository,
        private readonly ParameterBagInterface $parameterBag
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('annee', InputArgument::REQUIRED, 'Indiquée l\'année')
            ->addArgument('type', InputArgument::REQUIRED, 'Choix:'.implode(',', $this->types));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $year = (int)$input->getArgument('annee');
        $type = $input->getArgument('type');

        if (!\in_array($type, $this->types)) {
            $io->error('Erreur pour le type, les choix possibles sont: '.implode(',', $this->types));

            return Command::FAILURE;
        }

        if (0 === $year) {
            $io->error('Erreur pour année');

            return Command::FAILURE;
        }

        $fileName = "Pesees$year.csv";
        $file = $this->parameterBag->get('kernel.project_dir').'/data/'.$fileName;

        if ('duobac' === $type) {
            $i = 0;
            $this->peseeRepository->removeByYear($year);
            $spl = $this->importManager->read($file);

            foreach ($spl as $key => $data) {
                if ($key === 0) {
                    continue;
                }

                if ($this->importManager->skip($data[0])) {
                    $io->writeln($data[0]);
                    continue;
                }
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
