<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 16/11/18
 * Time: 10:15
 */

namespace AcMarche\Duobac\Manager;

use AcMarche\Duobac\Entity\Pesee;
use AcMarche\Duobac\Entity\PeseeMoyenne;
use AcMarche\Duobac\Repository\PeseeMoyenneRepository;
use AcMarche\Duobac\Repository\PeseeRepository;
use AcMarche\Duobac\Repository\SituationFamilialeRepository;
use AcMarche\Duobac\Service\DateUtils;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoyenneManager
{
    /**
     * @var PeseeMoyenneRepository
     */
    private $moyenneRepository;
    /**
     * @var PeseeRepository
     */
    private $peseeRepository;
    /**
     * @var SituationFamilialeRepository
     */
    private $situationFamilialeRepository;
    /**
     * @var DateUtils
     */
    private $dateUtils;
    /**
     * @var SymfonyStyle
     */
    private $io;
    /**
     * @var DuobacManager
     */
    private $duobacManager;

    public function __construct(
        PeseeMoyenneRepository $moyenneRepository,
        PeseeRepository $peseeRepository,
        SituationFamilialeRepository $situationFamilialeRepository,
        DateUtils $dateUtils,
        DuobacManager $duobacManager
    ) {
        $this->moyenneRepository = $moyenneRepository;
        $this->peseeRepository = $peseeRepository;
        $this->situationFamilialeRepository = $situationFamilialeRepository;
        $this->dateUtils = $dateUtils;
        $this->duobacManager = $duobacManager;
    }

    public function execute(int $year)
    {
        $duobacs = $this->duobacManager->getDuobacsCitoyens();
        $puces = $this->duobacManager->getPucesCitoyensByDuobacs($duobacs);
        $this->updateMoyenne($puces, $year);
    }

    public function getInstance(string $charge, \DateTimeInterface $dateTime): PeseeMoyenne
    {
        if (!$moyenne = $this->moyenneRepository->findOneBy(['date_pesee' => $dateTime, 'a_charge' => $charge])) {
            $moyenne = new PeseeMoyenne();
            $moyenne->setDatePesee($dateTime);
            $moyenne->setACharge($charge);
            $this->moyenneRepository->persist($moyenne);
        }

        return $moyenne;
    }

    /**
     * @param int $year
     * @throws \Exception
     */
    public function updateMoyenne(array $puces, int $year)
    {
        $charges = $this->situationFamilialeRepository->getListeCharges();

        foreach ($charges as $charge) {
            $this->io->title('A charge ' . $charge['a_charge']);
            foreach (DateUtils::getListeNumeroMoisWith2digits() as $mois) {
                $yearMonth = $year . "-" . $mois;
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
                $date = $this->dateUtils->convertStringToDateTime($yearMonth . '-01', 'Y-m-d');

                $peseeMoyenne = $this->getInstance($charge['a_charge'], $date);
                $peseeMoyenne->setPoids($moyenne);
            }
        }
          $this->moyenneRepository->flush();
    }

    /**
     * @param int $charge
     * @param \DateTimeInterface $dateTime
     * @return PeseeMoyenne|null
     *
     */
    public function findOneByChargeAndDate(int $charge, \DateTime $dateTime)
    {
        try {
            return $this->moyenneRepository->findOneByChargeAndDate($charge, $dateTime);
        } catch (NonUniqueResultException $e) {
        }

        return null;
    }

    /**
     * @param Pesee[] $pesees
     */
    public function setMoyennes(iterable $pesees)
    {
        foreach ($pesees as $pesee) {
            $date = $pesee->getDatePesee();
            $charge = $pesee->getACharge();
            $moyenne = $this->findOneByChargeAndDate($charge, $date);
            $pesee->setMoyenne($moyenne);
        }
    }

    public function setIo(SymfonyStyle $io)
    {
        $this->io = $io;
    }
}
