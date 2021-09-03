<?php

namespace AcMarche\Duobac\Repository;

use AcMarche\Duobac\Doctrine\OrmCrudTrait;
use AcMarche\Duobac\Entity\SituationFamiliale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SituationFamiliale|null find($id, $lockMode = null, $lockVersion = null)
 * @method SituationFamiliale|null findOneBy(array $criteria, array $orderBy = null)
 * @method SituationFamiliale[]    findAll()
 * @method SituationFamiliale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SituationFamilialeRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SituationFamiliale::class);
    }

    /**
     * Retourne les differentes charges existantes
     * @return iterable ['a_charge'=>0]
     */
    public function getListeCharges()
    {
        return $this->createQueryBuilder('situation_familiale')
            ->select('situation_familiale.a_charge')
            ->groupBy('situation_familiale.a_charge')
            ->orderBy('situation_familiale.a_charge', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @param string $rdvMatricule
     * @return SituationFamiliale[]
     */
    public function findByMatricule(string $rdvMatricule): array
    {
        return $this->createQueryBuilder('situation_familiale')
            ->andWhere('situation_familiale.rdv_matricule = :matricule')
            ->setParameter('matricule', $rdvMatricule)
            ->orderBy('situation_familiale.annee', 'ASC')
            ->getQuery()->getResult();
    }

    public function getAllYearsByMatricule(string $matricule): array
    {
        return array_unique(
            array_map(function ($situation) {
                return $situation->getAnnee();
            }, $this->findByMatricule($matricule))
        );
    }

    public function getChargeByMatriculeAndYear($rdvMatricule, $year): int
    {
        $situations = $this->createQueryBuilder('situation_familiale')
            ->andWhere('situation_familiale.annee = :annee')
            ->setParameter('annee', $year)
            ->andWhere('situation_familiale.rdv_matricule = :matricule')
            ->setParameter('matricule', $rdvMatricule)
            ->orderBy('situation_familiale.a_charge', 'ASC')
            ->getQuery()->getResult();

        if (count($situations) > 0) {
            return $situations[0]->getACharge();
        }

        return 0;
    }

    /**
     * @param string $rdvMatricule
     * @param int $year
     * @return array|SituationFamiliale[]|SituationFamiliale
     */
    public function findByMatriculeAndYear(string $rdvMatricule, int $year, bool $one = false)
    {
        $situations = $this->createQueryBuilder('situation_familiale')
            ->andWhere('situation_familiale.annee = :annee')
            ->setParameter('annee', $year)
            ->andWhere('situation_familiale.rdv_matricule = :matricule')
            ->setParameter('matricule', $rdvMatricule)
            ->orderBy('situation_familiale.a_charge', 'ASC')
            ->getQuery()->getResult();

        if ($one === false) {
            return $situations;
        }

        if (count($situations) > 0) {
            return $situations[0];
        }

        return null;
    }
}
