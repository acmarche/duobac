<?php

namespace AcMarche\Duobac\Repository;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Duobac::class);
    }

    public function persist(Duobac $duobac)
    {
        $this->_em->persist($duobac);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function remove(Duobac $duobac)
    {
        $this->_em->remove($duobac);
        $this->flush();
    }
}
