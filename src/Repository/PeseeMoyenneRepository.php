<?php

namespace AcMarche\Duobac\Repository;

use AcMarche\Duobac\Doctrine\OrmCrudTrait;
use AcMarche\Duobac\Entity\PeseeMoyenne;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PeseeMoyenne|null find($id, $lockMode = null, $lockVersion = null)
 * @method PeseeMoyenne|null findOneBy(array $criteria, array $orderBy = null)
 * @method PeseeMoyenne[]    findAll()
 * @method PeseeMoyenne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeseeMoyenneRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PeseeMoyenne::class);
    }

    /**
     * @param int $charge
     * @param DateTimeInterface $dateTime
     * @return PeseeMoyenne|null
     */
    public function findOneByChargeAndDate(int $charge, DateTimeInterface $dateTime)
    {
        return $this->createQueryBuilder('pesee_moyenne')
            ->andWhere('pesee_moyenne.a_charge = :charge')
            ->setParameter('charge', $charge)
            ->andWhere('pesee_moyenne.date_pesee LIKE :date')
            ->setParameter('date', $dateTime->format('Y-m')."%")
            ->orderBy('pesee_moyenne.date_pesee', 'ASC')
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param int $charge
     * @param int $year
     * @return PeseeMoyenne[]|null
     */
    public function findOneByChargeAndYear(int $charge, int $year)
    {
        return $this->createQueryBuilder('pesee_moyenne')
            ->andWhere('pesee_moyenne.a_charge = :charge')
            ->setParameter('charge', $charge)
            ->andWhere('pesee_moyenne.date_pesee LIKE :year')
            ->setParameter('year', $year."%")
            ->orderBy('pesee_moyenne.date_pesee', 'ASC')
            ->getQuery()->getResult();
    }
}
