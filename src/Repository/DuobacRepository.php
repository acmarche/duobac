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

    public function loadUserByIdentifier(string $rrn, string $puce): ?Duobac
    {
        return $this->createQueryBuilder('duobac')
            ->andWhere('duobac.rdv_matricule = :rrn')
            ->setParameter('rrn', $rrn)
            ->andWhere('duobac.puc_no_puce = :puce')
            ->setParameter('puce', $puce)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
