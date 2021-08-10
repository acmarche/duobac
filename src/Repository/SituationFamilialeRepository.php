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
        $builder = $this->createQueryBuilder('situation_familiale');

        $builder
            ->select('situation_familiale.a_charge')
            ->groupBy('situation_familiale.a_charge')
            ->orderBy('situation_familiale.a_charge', 'ASC');

        return $builder->getQuery()->getResult();
    }

    /**
     * @param string $rdvMatricule
     * @return SituationFamiliale[]
     */
    public function findByMatricule(string $rdvMatricule): array
    {
        $builder = $this->createQueryBuilder('situation_familiale');

        $builder
            ->andWhere('situation_familiale.rdv_matricule = :matricule')
            ->setParameter('matricule', $rdvMatricule)
            ->orderBy('situation_familiale.annee', 'ASC');

        return $builder->getQuery()->getResult();
    }

    /**
     * @param string $rdvMatricule
     * @param int $year
     * @return SituationFamiliale[]
     */
    public function findByMatriculeAndYear(string $rdvMatricule, int $year): array
    {
        $builder = $this->createQueryBuilder('situation_familiale');

        $builder
            ->andWhere('situation_familiale.annee = :annee')
            ->setParameter('annee', $year)
            ->andWhere('situation_familiale.rdv_matricule = :matricule')
            ->setParameter('matricule', $rdvMatricule)
            ->orderBy('situation_familiale.a_charge', 'ASC');

        return $builder->getQuery()->getResult();
    }
}
