<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 16/11/18
 * Time: 10:15
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
    private PeseeMoyenneRepository $peseeMoyenneRepository;
    private PeseeRepository $peseeRepository;
    private SituationFamilialeRepository $situationFamilialeRepository;
    private DateUtils $dateUtils;
    private ?SymfonyStyle $io = null;
    private DuobacRepository $duobacRepository;

    public function __construct(
        PeseeMoyenneRepository $peseeMoyenneRepository,
        PeseeRepository $peseeRepository,
        DuobacRepository $duobacRepository,
        SituationFamilialeRepository $situationFamilialeRepository,
        DateUtils $dateUtils
    ) {
        $this->peseeMoyenneRepository = $peseeMoyenneRepository;
        $this->peseeRepository = $peseeRepository;
        $this->situationFamilialeRepository = $situationFamilialeRepository;
        $this->dateUtils = $dateUtils;
        $this->duobacRepository = $duobacRepository;
    }

    public function execute(int $year): void
    {
        $duobacs = $this->duobacRepository->getDuobacsCitoyens();
        $puces = $this->duobacRepository->getPucesCitoyensByDuobacs($duobacs);
        $this->updateMoyenne($puces, $year);
    }

    public function getInstance(string $charge, DateTimeInterface $dateTime): PeseeMoyenne
    {
        if (($moyenne = $this->peseeMoyenneRepository->findOneBy(['date_pesee' => $dateTime, 'a_charge' => $charge]
            )) === null) {
            $moyenne = new PeseeMoyenne();
            $moyenne->setDatePesee($dateTime);
            $moyenne->setACharge($charge);
            $this->peseeMoyenneRepository->persist($moyenne);
        }

        return $moyenne;
    }

    /**
     * @param int $year
     * @throws Exception
     */
    public function updateMoyenne(array $puces, int $year): void
    {
        $charges = $this->situationFamilialeRepository->getListeCharges();

        foreach ($charges as $charge) {
            $this->io->title('A charge '.$charge['a_charge']);
            foreach (DateUtils::getListeNumeroMoisWith2digits() as $mois) {
                $yearMonth = $year."-".$mois;
                $this->io->writeln($yearMonth);

                $pesees = $this->peseeRepository->getPeseesByChargeByYearMonth($puces, $charge['a_charge'], $yearMonth);
                $total = $moyenne = 0;
                $count = count($pesees);
                if ($count > 0) {
                    foreach ($pesees as $pesee) {
                        $poids = $pesee->getPoids();
                        //    $this->io->writeln($poids);
                        $total += $poids;
                    }
                    $moyenne = $total / $count;
                }
                //$io->writeln($total.' / '.$count);
                //$io->writeln($moyenne);
                $date = $this->dateUtils->convertStringToDateTime($yearMonth.'-01', 'Y-m-d');

                $peseeMoyenne = $this->getInstance($charge['a_charge'], $date);
                $peseeMoyenne->setPoids($moyenne);
            }
        }
        $this->peseeMoyenneRepository->flush();
    }

    public function setIo(SymfonyStyle $io): void
    {
        $this->io = $io;
    }

    public function deleteByYear(int $year) {
        $this->peseeMoyenneRepository->deleteByYear($year);
    }
}
