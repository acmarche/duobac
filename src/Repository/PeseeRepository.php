<?php

namespace AcMarche\Duobac\Repository;

use AcMarche\Duobac\Entity\Pesee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pesee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pesee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pesee[]    findAll()
 * @method Pesee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeseeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pesee::class);
    }

    public function persist(Pesee $releve)
    {
        $this->_em->persist($releve);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function remove(Pesee $releve)
    {
        $this->_em->remove($releve);
        $this->flush();
    }

    /**
     * @return Pesee[] Returns an array of Releve objects
     */
    public function findByYear(int $annee)
    {
        $builder = $this->createQueryBuilder('pesee');

        $builder
            ->andWhere('pesee.date_pesee LIKE :annee')
            ->setParameter('annee', $annee."%");

        $builder->orderBy('pesee.date_pesee', 'ASC');

        return $builder->getQuery()->getResult();
    }

    /**
     * @return Pesee[] Returns an array of Releve objects
     */
    public function findByPuceAndYear(
        string $puce,
        int $year,
        \DateTimeInterface $dateDebut = null,
        \DateTimeInterface $dateFin = null
    ) {
        $builder = $this->createQueryBuilder('pesee')
            ->orderBy('pesee.date_pesee', 'ASC');

        $builder
            ->andWhere('pesee.puc_no_puce = :puces')
            ->setParameter('puces', $puce);

        $builder
            ->andWhere('pesee.date_pesee LIKE :annee')
            ->setParameter('annee', $year."%");

        if ($dateDebut && $dateFin) {
            $builder->andWhere('pesee.date_pesee BETWEEN :debut AND :fin')
                ->setParameter('debut', $dateDebut)
                ->setParameter('fin', $dateFin);

            return $builder->getQuery()->getResult();
        }

        if ($dateDebut) {
            $builder
                ->andWhere('pesee.date_pesee > :month')
                ->setParameter('month', $dateDebut);

            return $builder->getQuery()->getResult();
        }

        if ($dateFin) {
            $builder
                ->andWhere('pesee.date_pesee < :month')
                ->setParameter('month', $dateFin);

            return $builder->getQuery()->getResult();
        }

        return $builder->getQuery()->getResult();
    }

    /**
     * @param array $puces
     * @param int $charge
     * @param string $yearMonth 2018-01
     * @return Pesee[]
     */
    public function getPeseesByChargeByYearMonth(array $puces, int $charge, string $yearMonth)
    {
        $builder = $this->createQueryBuilder('pesee');

        $builder
            ->andWhere('pesee.date_pesee LIKE :annee')
            ->setParameter('annee', $yearMonth."%")
            ->andWhere('pesee.a_charge LIKE :charge')
            ->setParameter('charge', $charge)
            ->andWhere('pesee.puc_no_puce IN (:puces)')
            ->setParameter('puces', $puces);

        $builder->orderBy('pesee.date_pesee', 'ASC');

        return $builder->getQuery()->getResult();
    }

    public function getYears(iterable $puces)
    {
        /**
         * SELECT YEAR(date_pesee) as year FROM `pesee` WHERE `puc_no_puce` LIKE '110028' GROUP BY year
         */
        $builder = $this->createQueryBuilder('pesee');

        $builder
            ->select("YEAR(pesee.date_pesee) as year")
            ->andWhere('pesee.puc_no_puce IN (:puces)')
            ->setParameter('puces', $puces)
            ->addGroupBy("year");

        return $builder->getQuery()->getResult();
    }
}
