<?php


namespace AcMarche\Duobac\Manager;

use AcMarche\Duobac\Entity\SituationFamiliale;
use AcMarche\Duobac\Entity\User;
use AcMarche\Duobac\Repository\SituationFamilialeRepository;

class SituationManager
{
    /**
     * @var SituationFamilialeRepository
     */
    private $situationFamilialeRepository;

    public function __construct(SituationFamilialeRepository $situationFamilialeRepository)
    {
        $this->situationFamilialeRepository = $situationFamilialeRepository;
    }

    public function getAllYears(User $user)
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
    public function getSituationsByMatriculeAndYear(string $matricule, int $year)
    {
        return $this->situationFamilialeRepository->findByMatriculeAndYear($matricule, $year);
    }

    /**
     * @param string $matricule
     * @param int $year
     * @return int
     */
    public function getChargeByMatriculeAndYear(string $matricule, int $year)
    {
        $situations = $this->getSituationsByMatriculeAndYear($matricule, $year);

        return $situations[0] ? $situations[0]->getACharge() : 0;
    }

    /**
     * @param string $matricule
     * @return SituationFamiliale[]|array
     */
    public function getSituationsByMatricule(string $matricule)
    {
        return $this->situationFamilialeRepository->findByMatricule($matricule);
    }
}
