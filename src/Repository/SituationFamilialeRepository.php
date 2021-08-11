<?php

namespace AcMarche\Duobac\Repository;

use AcMarche\Duobac\Doctrine\OrmCrudTrait;
use AcMarche\Duobac\Entity\SituationFamiliale;
use AcMarche\Duobac\Entity\User;
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

    public function getAllYears(User $user): array
    {
        $matricule = $user->getRdvMatricule();
        $years = [];
        $situations = $this->findByMatricule($matricule);
        foreach ($situations as $situation) {
            $years[] = $situation->getAnnee();
        }

        return array_unique($years);
    }

    public function getChargeByMatriculeAndYear($rdvMatricule, $year): int
    {
        $situation = $this->createQueryBuilder('situation_familiale')
            ->andWhere('situation_familiale.annee = :annee')
            ->setParameter('annee', $year)
            ->andWhere('situation_familiale.rdv_matricule = :matricule')
            ->setParameter('matricule', $rdvMatricule)
            ->orderBy('situation_familiale.a_charge', 'ASC')
            ->getQuery()->getOneOrNullResult();

        if ($situation) {
            return $situation->getCharge();
        }

        return 0;
    }
}
