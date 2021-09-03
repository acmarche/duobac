<?php

namespace AcMarche\Duobac\Pesee;

use AcMarche\Duobac\Entity\Pesee;
use AcMarche\Duobac\Entity\PeseeMoyenne;
use AcMarche\Duobac\Repository\PeseeMoyenneRepository;
use AcMarche\Duobac\Service\ArrayUtils;
use AcMarche\Duobac\Service\DateUtils;

class PeseeUtils
{
    private PeseeMoyenneRepository $peseeMoyenneRepository;

    public function __construct(
        PeseeMoyenneRepository $peseeMoyenneRepository
    ) {
        $this->peseeMoyenneRepository = $peseeMoyenneRepository;
    }

    /**
     * @param iterable|PeseeMoyenne[] $pesees
     */
    public function getTotal(iterable $pesees): float
    {
        $total = 0;
        foreach ($pesees as $pesee) {
            $total += $pesee->getPoids();
        }

        return $total;
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
     * @param array|Pesee[]|PeseeMoyenne[] $pesees
     */
    public function prepareForOneYear(array $pesees): array
    {
        $data = ArrayUtils::initArraMonths();
        foreach ($pesees as $pesee) {
            $data[$pesee->getDatePesee()->format('n')] += $pesee->getPoids();
        }

        return ArrayUtils::resetKeys($data);
    }


    /**
     * @param array|Pesee[]|PeseeMoyenne[] $data
     */
    public function prepareForAllYears(array $data): array
    {
        $all = [];
        foreach ($data as $year => $pesees) {
            $t = $this->prepareForOneYear($pesees);
            $all[$year] = array_sum($t);

        }
        return ($all);
    }

}
