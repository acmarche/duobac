<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 15/11/18
 * Time: 13:39
 */

namespace AcMarche\Duobac\Manager;

use AcMarche\Duobac\Entity\Pesee;
use AcMarche\Duobac\Entity\PeseeInterface;
use AcMarche\Duobac\Entity\PeseeMoyenne;
use AcMarche\Duobac\Repository\PeseeMoyenneRepository;
use AcMarche\Duobac\Repository\PeseeRepository;
use AcMarche\Duobac\Service\DateUtils;

class PeseeManager
{
    /**
     * @var PeseeRepository
     */
    private $peseeRepository;
    /**
     * @var DateUtils
     */
    private $dateUtils;

    /**
     * @var DuobacManager
     */
    private $duobacManager;
    /**
     * @var MoyenneManager
     */
    private $moyenneManager;
    /**
     * @var PeseeMoyenneRepository
     */
    private $peseeMoyenneRepository;
    /**
     * @var SituationManager
     */
    private $situationManager;

    public function __construct(
        PeseeRepository $peseeRepository,
        MoyenneManager $moyenneManager,
        PeseeMoyenneRepository $peseeMoyenneRepository,
        DuobacManager $duobacManager,
        SituationManager $situationManager,
        DateUtils $dateUtils
    ) {
        $this->peseeRepository = $peseeRepository;
        $this->dateUtils = $dateUtils;
        $this->duobacManager = $duobacManager;
        $this->moyenneManager = $moyenneManager;
        $this->peseeMoyenneRepository = $peseeMoyenneRepository;
        $this->situationManager = $situationManager;
    }

    public function getInstance(string $puc_no_puce, \DateTimeInterface $date_pesee, float $poids, int $aCharge): Pesee
    {
        if (!$pesee = $this->peseeRepository->findOneBy(
            ['puc_no_puce' => $puc_no_puce, 'date_pesee' => $date_pesee]
        )) {
            $pesee = new Pesee($puc_no_puce, $date_pesee, $poids, $aCharge);
            $this->peseeRepository->persist($pesee);
        }

        return $pesee;
    }

    /**
     * @param iterable|PeseeMoyenne[] $pesees
     * @return int
     */
    public function getTotal(iterable $pesees)
    {
        $total = 0;
        foreach ($pesees as $pesee) {
            $total += $pesee->getPoids();
        }

        return $total;
    }

    /**
     *
     * @param string $puce
     * @param int $year
     * @param \DateTimeInterface|null $dateDebut
     * @param \DateTimeInterface|null $dateFin
     * @return PeseeInterface[]
     */
    public function getPeseesByPuceAndYear(
        string $puce,
        int $year,
        \DateTimeInterface $dateDebut = null,
        \DateTimeInterface $dateFin = null
    ) {
        return $this->peseeRepository->findByPuceAndYear($puce, $year, $dateDebut, $dateFin);
    }

    /**
     * @param string $rdvMatricule
     * @param int $year
     * @return PeseeInterface[];
     *
     */
    public function getByMatriculeAndYear(string $rdvMatricule, int $year)
    {
        $situations = $this->situationManager->getSituationsByMatriculeAndYear($rdvMatricule, $year);

        $pesees = [[]];
        foreach ($situations as $situation) {
            $puce = $situation->getPucNoPuce();
            $duobac = $this->duobacManager->getDuobacByMatriculeAndPuce($rdvMatricule, $puce);

            $dateFin = $duobac->getPurDateFin();
            $dateDebut = $duobac->getPurDateDebut();
            $contrainteDebut = $contrainteFin = null;

            if ($dateDebut && (int)$dateDebut->format('Y') === $year) {
                $contrainteDebut = $dateDebut;
            }

            if ($dateFin && (int)$dateFin->format('Y') === $year) {
                $contrainteFin = $dateFin;
            }

            $pesees[] = $this->getPeseesByPuceAndYear($puce, $year, $contrainteDebut, $contrainteFin);
        }

        return array_merge(...$pesees);
    }

    /**
     *
     * @param array $pesees
     * @param int $year
     *
     * @return array
     */
    public function setMissingMonths(array $pesees, int $year, int $charge)
    {
        foreach (DateUtils::getListeNumeroMoisWithOnedigit() as $numMois) {
            if (!isset($pesees[$numMois])) {
                $pesees[$numMois]['poids'] = 0;

                $date = \DateTime::createFromFormat('Y-m-d', $year . '-' . $numMois . '-01');
                $menage = $data['menage'] ?? $this->peseeMoyenneRepository->findOneByChargeAndDate($charge, $date);
                if(!$menage){
                    $pesees[$numMois]['menage'] = 0;
                }
                $pesees[$numMois]['menage'] = $menage->getPoids();
            }
        }

        ksort($pesees);

        return $pesees;
    }

    /**
     * @param iterable|Pesee[] $pesees
     * @return array
     */
    public function groupPeseesByMonth(iterable $pesees)
    {
        $all = [];
        foreach ($pesees as $pesee) {
            $mois = (int)$pesee->getDatePesee()->format('m');
            $poids = $pesee->getPoids();
            $menagePoids = $pesee->getMoyenne() ? $pesee->getMoyenne()->getPoids() : 0;

            isset($all[$mois]['poids']) ? $all[$mois]['poids'] += $poids : $all[$mois]['poids'] = $poids;
            $all[$mois]['menage'] = $menagePoids;
        }

        return $all;
    }

    /**
     * @param iterable|Pesee[] $pesees
     * @return Pesee[]
     */
    public function groupPeseesByMonth22(iterable $pesees)
    {
        $data = [];
        foreach ($pesees as $pesee) {
            $mois = (int)$pesee->getDatePesee()->format('m');

            if (!isset($data[$mois])) {
                $data[$mois] = $pesee;
            } else {
                /**
                 * @var Pesee $current
                 */
                $current = $data[$mois];
                $data[$mois] = $current->setPoids($current->getPoids() + $pesee->getPoids());
            }
        }

        return $data;
    }

    public function flush()
    {
        $this->peseeRepository->flush();
    }

    public function persist(Pesee $pesee)
    {
        $this->peseeRepository->persist($pesee);
    }
}
