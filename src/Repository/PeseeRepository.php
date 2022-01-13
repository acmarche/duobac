<?php

namespace AcMarche\Duobac\Repository;

use AcMarche\Duobac\Doctrine\OrmCrudTrait;
use AcMarche\Duobac\Entity\Duobac;
use AcMarche\Duobac\Entity\Pesee;
use AcMarche\Duobac\Entity\PeseeInterface;
use DateTimeInterface;
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
    use OrmCrudTrait;

    public function __construct(
        ManagerRegistry $registry,
        private SituationFamilialeRepository $situationFamilialeRepository,
        private DuobacRepository $duobacRepository
    ) {
        parent::__construct($registry, Pesee::class);
    }

    /**
     * @return Pesee[] Returns an array of Releve objects
     */
    public function findByPuceAndDatesConstraint(
        string $puce,
        DateTimeInterface $dateDebut,
        ?DateTimeInterface $dateFin = null
    ): array {
        $builder = $this->createQueryBuilder('pesee')
            ->orderBy('pesee.date_pesee', 'ASC')
            ->andWhere('pesee.puc_no_puce = :puces')
            ->setParameter('puces', $puce);

        if (null !== $dateFin) {
            $builder->andWhere('pesee.date_pesee BETWEEN :debut AND :fin')
                ->setParameter('debut', $dateDebut)
                ->setParameter('fin', $dateFin);
        } else {
            $builder
                ->andWhere('pesee.date_pesee > :debut')
                ->setParameter('debut', $dateDebut);
        }

        return $builder->getQuery()->getResult();
    }

    /**
     * @param string $yearMonth 2018-01
     *
     * @return Pesee[]
     */
    public function getPeseesByChargeByYearMonth(array $puces, int $charge, string $yearMonth): array
    {
        return $this->createQueryBuilder('pesee')
            ->andWhere('pesee.date_pesee LIKE :annee')
            ->setParameter('annee', $yearMonth.'%')
            ->andWhere('pesee.a_charge LIKE :charge')
            ->setParameter('charge', $charge)
            ->andWhere('pesee.puc_no_puce IN (:puces)')
            ->setParameter('puces', $puces)
            ->orderBy('pesee.date_pesee', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @param array|Duobac[] $duobacs
     *
     * @return PeseeInterface[]
     */
    public function findByDuobacsAndYear(array $duobacs, int $year): array
    {
        $pesees = [[]];
        foreach ($duobacs as $duobac) {
            $pesees[] = $this->findByPuceAndDatesConstraint($duobac->getPucNoPuce(), $duobac->getPurDateDebut(), $duobac->getPurDateFin());
        }
        $pesees = array_merge(...$pesees);
        if (0 !== $year) {
            $pesees = array_filter($pesees, fn ($pesee) => $pesee->getDatePesee()->format('Y') == $year);
        }

        return $pesees;
    }

    public function removeByYear(int $year): void
    {
        $pesees = $this->createQueryBuilder('pesee')
            ->orderBy('pesee.date_pesee', 'ASC')
            ->andWhere('pesee.date_pesee LIKE :annee')
            ->setParameter('annee', $year.'%')
            ->getQuery()
            ->getResult();

        foreach ($pesees as $pesee) {
            $this->remove($pesee);
        }

        $this->flush();
    }
}
