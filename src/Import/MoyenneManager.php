<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 16/11/18
 * Time: 10:15.
 */

namespace AcMarche\Duobac\Import;

use AcMarche\Duobac\Entity\PeseeMoyenne;
use AcMarche\Duobac\Repository\DuobacRepository;
use AcMarche\Duobac\Repository\PeseeMoyenneRepository;
use AcMarche\Duobac\Repository\PeseeRepository;
use AcMarche\Duobac\Repository\SituationFamilialeRepository;
use AcMarche\Duobac\Service\DateUtils;
use DateTimeInterface;
use Exception;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoyenneManager
{
    private ?SymfonyStyle $io = null;

    public function __construct(
        private readonly PeseeMoyenneRepository $peseeMoyenneRepository,
        private readonly PeseeRepository $peseeRepository,
        private readonly DuobacRepository $duobacRepository,
        private readonly SituationFamilialeRepository $situationFamilialeRepository,
        private readonly DateUtils $dateUtils
    ) {
    }

    public function execute(int $year): void
    {
        $duobacs = $this->duobacRepository->getDuobacsCitoyens();
        $puces = $this->duobacRepository->getPucesCitoyensByDuobacs($duobacs);
        $this->updateMoyenne($puces, $year);
    }

    public function getInstance(string $charge, DateTimeInterface $dateTime): PeseeMoyenne
    {
        if (($moyenne = $this->peseeMoyenneRepository->findOneBy(
                ['date_pesee' => $dateTime, 'a_charge' => $charge]
            )) === null) {
            $moyenne = new PeseeMoyenne();
            $moyenne->date_pesee = $dateTime;
            $moyenne->a_charge = $charge;
            $this->peseeMoyenneRepository->persist($moyenne);
        }

        return $moyenne;
    }

    /**
     * @throws Exception
     */
    public function updateMoyenne(array $puces, int $year): void
    {
        $charges = $this->situationFamilialeRepository->getListeCharges();

        foreach ($charges as $charge) {
            $this->io->title('A charge '.$charge['a_charge']);
            foreach (DateUtils::getListeNumeroMoisWith2digits() as $mois) {
                $yearMonth = $year.'-'.$mois;
                $this->io->writeln($yearMonth);

                $pesees = $this->peseeRepository->getPeseesByChargeByYearMonth($puces, $charge['a_charge'], $yearMonth);
                $total = $moyenne = 0;
                $count = \count($pesees);
                if ($count > 0) {
                    foreach ($pesees as $pesee) {
                        $poids = $pesee->poids;
                        //    $this->io->writeln($poids);
                        $total += $poids;
                    }
                    $moyenne = $total / $count;
                }
                //$io->writeln($total.' / '.$count);
                //$io->writeln($moyenne);
                $date = $this->dateUtils->convertStringToDateTime($yearMonth.'-01', 'Y-m-d');

                $peseeMoyenne = $this->getInstance($charge['a_charge'], $date);
                $peseeMoyenne->poids = $moyenne;
            }
        }
        $this->peseeMoyenneRepository->flush();
    }

    public function setIo(SymfonyStyle $io): void
    {
        $this->io = $io;
    }

    public function deleteByYear(int $year): void
    {
        $this->peseeMoyenneRepository->deleteByYear($year);
    }
}
