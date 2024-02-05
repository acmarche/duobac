<?php

namespace AcMarche\Duobac\Pesee;

use AcMarche\Duobac\Entity\Pesee;
use AcMarche\Duobac\Entity\PeseeMoyenne;
use AcMarche\Duobac\Repository\PeseeMoyenneRepository;
use AcMarche\Duobac\Service\ArrayUtils;
use AcMarche\Duobac\Service\DateUtils;
use DateTime;

class PeseeUtils
{
    public function __construct(private readonly PeseeMoyenneRepository $peseeMoyenneRepository)
    {
    }

    /**
     * @param iterable|PeseeMoyenne[] $pesees
     */
    public function getTotal(iterable $pesees): float|int
    {
        $total = 0;
        foreach ($pesees as $pesee) {
            $total += $pesee->poids;
        }

        return $total;
    }

    public function setMissingMonths(array $pesees, int $year, int $charge): array
    {
        foreach (DateUtils::getListeNumeroMoisWithOnedigit() as $numMois) {
            if (!isset($pesees[$numMois])) {
                $pesees[$numMois]['poids'] = 0;

                $date = DateTime::createFromFormat('Y-m-d', $year.'-'.$numMois.'-01');
                $menage = $data['menage'] ?? $this->peseeMoyenneRepository->findOneByChargeAndDate($charge, $date);
                $pesees[$numMois]['menage'] = $menage ? $menage->poids : 0;
            }
        }

        ksort($pesees);

        return $pesees;
    }

    /**
     * @param Pesee[] $pesees
     */
    public function groupPeseesByMonth(iterable $pesees): array
    {
        $all = [];
        foreach ($pesees as $pesee) {
            $mois = (int)$pesee->date_pesee->format('m');
            $poids = $pesee->poids;
            $menagePoids = null !== $pesee->moyenne ? $pesee->moyenne->poids : 0;

            isset($all[$mois]['poids']) ? $all[$mois]['poids'] += $poids : $all[$mois]['poids'] = $poids;
            $all[$mois]['menage'] = $menagePoids;
        }

        return $all;
    }

    /**
     * @param array|Pesee[]|PeseeMoyenne[] $pesees
     */
    public function groupByMonthsForOneYear(array $pesees): array
    {
        $data = ArrayUtils::initArraMonths();
        foreach ($pesees as $pesee) {
            $data[$pesee->date_pesee->format('n')] += $pesee->poids;
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
            $t = $this->groupByMonthsForOneYear($pesees);
            $all[$year] = array_sum($t);
        }

        return $all;
    }
}
