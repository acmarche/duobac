<?php


namespace AcMarche\Duobac\Manager;

use AcMarche\Duobac\Entity\SituationFamiliale;
use AcMarche\Duobac\Entity\User;
use AcMarche\Duobac\Repository\SituationFamilialeRepository;

class SituationManager
{
    private SituationFamilialeRepository $situationFamilialeRepository;

    public function __construct(SituationFamilialeRepository $situationFamilialeRepository)
    {
        $this->situationFamilialeRepository = $situationFamilialeRepository;
    }

    public function getAllYears(User $user): array
    {
        $matricule = $user->getRdvMatricule();
        $years = [];
        $situations = $this->situationFamilialeRepository->findByMatricule($matricule);
        foreach ($situations as $situation) {
            $years[] = $situation->getAnnee();
        }

        return array_unique($years);
    }

    /**
     * @param string $matricule
     * @param int $year
     * @return SituationFamiliale[]|array
     */
    public function getSituationsByMatriculeAndYear(string $matricule, int $year): array
    {
        return $this->situationFamilialeRepository->findByMatriculeAndYear($matricule, $year);
    }

    /**
     * @param string $matricule
     * @param int $year
     */
    public function getChargeByMatriculeAndYear(string $matricule, int $year): ?int
    {
        $situations = $this->getSituationsByMatriculeAndYear($matricule, $year);

        return $situations[0] ? $situations[0]->getACharge() : 0;
    }

    /**
     * @param string $matricule
     * @return SituationFamiliale[]|array
     */
    public function getSituationsByMatricule(string $matricule): array
    {
        return $this->situationFamilialeRepository->findByMatricule($matricule);
    }
}
