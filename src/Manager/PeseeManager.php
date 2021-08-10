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
use DateTime;
use DateTimeInterface;

class PeseeManager
{
    private PeseeRepository $peseeRepository;
    private DuobacManager $duobacManager;
    private PeseeMoyenneRepository $peseeMoyenneRepository;
    private SituationManager $situationManager;

    public function __construct(
        PeseeRepository $peseeRepository,
        PeseeMoyenneRepository $peseeMoyenneRepository,
        DuobacManager $duobacManager,
        SituationManager $situationManager
    ) {
        $this->peseeRepository = $peseeRepository;
        $this->duobacManager = $duobacManager;
        $this->peseeMoyenneRepository = $peseeMoyenneRepository;
        $this->situationManager = $situationManager;
    }

    public function getInstance(string $puc_no_puce, DateTimeInterface $date_pesee, float $poids, int $aCharge): Pesee
    {
        if (($pesee = $this->peseeRepository->findOneBy(
                ['puc_no_puce' => $puc_no_puce, 'date_pesee' => $date_pesee]
            )) === null) {
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
     * @param DateTimeInterface|null $dateDebut
     * @param DateTimeInterface|null $dateFin
     * @return PeseeInterface[]
     */
    public function getPeseesByPuceAndYear(
        string $puce,
        int $year,
        DateTimeInterface $dateDebut = null,
        DateTimeInterface $dateFin = null
    ): array {
        return $this->peseeRepository->findByPuceAndYear($puce, $year, $dateDebut, $dateFin);
    }

    /**
     * @param string $rdvMatricule
     * @param int $year
     * @return PeseeInterface[];
     *
     */
    public function getByMatriculeAndYear(string $rdvMatricule, int $year): array
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
     */
    public function setMissingMonths(array $pesees, int $year, int $charge): array
    {
        foreach (DateUtils::getListeNumeroMoisWithOnedigit() as $numMois) {
            if (!isset($pesees[$numMois])) {
                $pesees[$numMois]['poids'] = 0;

                $date = DateTime::createFromFormat('Y-m-d', $year.'-'.$numMois.'-01');
                $menage = $data['menage'] ?? $this->peseeMoyenneRepository->findOneByChargeAndDate($charge, $date);
                $pesees[$numMois]['menage'] = $menage ? $menage->getPoids() : 0;
            }
        }

        ksort($pesees);

        return $pesees;
    }

    /**
     * @param iterable|Pesee[] $pesees
     */
    public function groupPeseesByMonth(iterable $pesees): array
    {
        $all = [];
        foreach ($pesees as $pesee) {
            $mois = (int)$pesee->getDatePesee()->format('m');
            $poids = $pesee->getPoids();
            $menagePoids = $pesee->getMoyenne() !== null ? $pesee->getMoyenne()->getPoids() : 0;

            isset($all[$mois]['poids']) ? $all[$mois]['poids'] += $poids : $all[$mois]['poids'] = $poids;
            $all[$mois]['menage'] = $menagePoids;
        }

        return $all;
    }

    /**
     * @param iterable|Pesee[] $pesees
     * @return Pesee[]
     */
    public function groupPeseesByMonth22(iterable $pesees): array
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

    public function flush(): void
    {
        $this->peseeRepository->flush();
    }

    public function persist(Pesee $pesee): void
    {
        $this->peseeRepository->persist($pesee);
    }
}
