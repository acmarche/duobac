<?php

namespace AcMarche\Duobac\Repository;

use AcMarche\Duobac\Entity\PeseeMoyenne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method PeseeMoyenne|null find($id, $lockMode = null, $lockVersion = null)
 * @method PeseeMoyenne|null findOneBy(array $criteria, array $orderBy = null)
 * @method PeseeMoyenne[]    findAll()
 * @method PeseeMoyenne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeseeMoyenneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PeseeMoyenne::class);
    }

    public function persist(PeseeMoyenne $moyenne)
    {
        $this->_em->persist($moyenne);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function remove(PeseeMoyenne $moyenne)
    {
        $this->_em->remove($moyenne);
        $this->flush();
    }

    /**
     * @param int $charge
     * @param \DateTimeInterface $dateTime
     * @return PeseeMoyenne|null
     *
     */
    public function findOneByChargeAndDate(int $charge, \DateTimeInterface $dateTime)
    {
        $builder = $this->createQueryBuilder('pesee_moyenne');

        $builder
            ->andWhere('pesee_moyenne.a_charge = :charge')
            ->setParameter('charge', $charge);

        $builder
            ->andWhere('pesee_moyenne.date_pesee LIKE :date')
            ->setParameter('date', $dateTime->format('Y-m')."%");

        $builder->orderBy('pesee_moyenne.date_pesee', 'ASC');

        try {
            return $builder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }

        return null;
    }

    /**
     * @param int $charge
     * @param int $year
     * @return PeseeMoyenne[]|null
     *
     */
    public function findOneByChargeAndYear(int $charge, int $year)
    {
        $builder = $this->createQueryBuilder('pesee_moyenne');

        $builder
            ->andWhere('pesee_moyenne.a_charge = :charge')
            ->setParameter('charge', $charge);

        $builder
            ->andWhere('pesee_moyenne.date_pesee LIKE :year')
            ->setParameter('year', $year."%");

        $builder->orderBy('pesee_moyenne.date_pesee', 'ASC');

        return $builder->getQuery()->getResult();
    }
}
