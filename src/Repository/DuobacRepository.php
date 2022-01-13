<?php

namespace AcMarche\Duobac\Repository;

use AcMarche\Duobac\Doctrine\OrmCrudTrait;
use AcMarche\Duobac\Entity\Duobac;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Duobac|null find($id, $lockMode = null, $lockVersion = null)
 * @method Duobac|null findOneBy(array $criteria, array $orderBy = null)
 * @method Duobac[]    findAll()
 * @method Duobac[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DuobacRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Duobac::class);
    }

    public function findByRrnAndPuce(string $rrn, string $puce): ?Duobac
    {
        return $this->createQueryBuilder('duobac')
            ->andWhere('duobac.rdv_matricule = :rrn')
            ->setParameter('rrn', $rrn)
            ->andWhere('duobac.puc_no_puce = :puce')
            ->setParameter('puce', $puce)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Duobac[] $duobacs
     *
     * @return string[]
     */
    public function getPucesCitoyensByDuobacs(array $duobacs): array
    {
        $puces = [];
        foreach ($duobacs as $duobac) {
            if (11 === \strlen($duobac->getRdvMatricule())) {
                $puces[] = $duobac->getPucNoPuce();
            }
        }

        return $puces;
    }

    /**
     * @return Duobac[]
     */
    public function getDuobacsCitoyens(): array
    {
        $data = $this->findAll();
        $duobacs = [];
        foreach ($data as $duobac) {
            if (11 == \strlen($duobac->getRdvMatricule())) {
                $duobacs[] = $duobac;
            }
        }

        return $duobacs;
    }

    /**
     * @param $matricule
     *
     * @return array|Duobac[]
     */
    public function findByMatricule($matricule): array
    {
        return $this->createQueryBuilder('duobac')
            ->andWhere('duobac.rdv_matricule = :matricule')
            ->setParameter('matricule', $matricule)
            ->getQuery()->getResult();
    }

    public function findOneByMatriculeAndPuce($matricule, $puce): ?Duobac
    {
        return $this->createQueryBuilder('duobac')
            ->andWhere('duobac.rdv_matricule = :matricule')
            ->setParameter('matricule', $matricule)
            ->andWhere('duobac.puc_no_puce = :puce')
            ->setParameter('puce', $puce)
            ->getQuery()->getOneOrNullResult();
    }
}
