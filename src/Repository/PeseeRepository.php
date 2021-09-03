<?php

namespace AcMarche\Duobac\Repository;

use AcMarche\Duobac\Doctrine\OrmCrudTrait;
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

    private SituationFamilialeRepository $situationFamilialeRepository;
    private DuobacRepository $duobacRepository;

    public function __construct(
        ManagerRegistry $registry,
        SituationFamilialeRepository $situationFamilialeRepository,
        DuobacRepository $duobacRepository
    ) {
        parent::__construct($registry, Pesee::class);
        $this->situationFamilialeRepository = $situationFamilialeRepository;
        $this->duobacRepository = $duobacRepository;
    }

    /**
     * @return Pesee[] Returns an array of Releve objects
     */
    public function findByPuceAndYear(
        string $puce,
        int $year,
        DateTimeInterface $dateDebut = null,
        DateTimeInterface $dateFin = null
    ): array {
        $builder = $this->createQueryBuilder('pesee')
            ->orderBy('pesee.date_pesee', 'ASC')
            ->andWhere('pesee.puc_no_puce = :puces')
            ->setParameter('puces', $puce)
            ->andWhere('pesee.date_pesee LIKE :annee')
            ->setParameter('annee', $year."%");

        if ($dateDebut && $dateFin) {
            $builder->andWhere('pesee.date_pesee BETWEEN :debut AND :fin')
                ->setParameter('debut', $dateDebut)
                ->setParameter('fin', $dateFin);

            return $builder->getQuery()->getResult();
        }

        if ($dateDebut !== null) {
            $builder
                ->andWhere('pesee.date_pesee > :month')
                ->setParameter('month', $dateDebut);

            return $builder->getQuery()->getResult();
        }

        if ($dateFin !== null) {
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
    public function getPeseesByChargeByYearMonth(array $puces, int $charge, string $yearMonth): array
    {
        return $this->createQueryBuilder('pesee')
            ->andWhere('pesee.date_pesee LIKE :annee')
            ->setParameter('annee', $yearMonth."%")
            ->andWhere('pesee.a_charge LIKE :charge')
            ->setParameter('charge', $charge)
            ->andWhere('pesee.puc_no_puce IN (:puces)')
            ->setParameter('puces', $puces)
            ->orderBy('pesee.date_pesee', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @param string $rdvMatricule
     * @param int $year
     * @return PeseeInterface[]
     */
    public function getByMatriculeAndYear(string $rdvMatricule, int $year): array
    {
        $situations = $this->situationFamilialeRepository->findByMatriculeAndYear($rdvMatricule, $year);

        $pesees = [[]];
        foreach ($situations as $situation) {
            $puce = $situation->getPucNoPuce();
            $duobac = $this->duobacRepository->findOneByMatriculeAndPuce($rdvMatricule, $puce);

            $dateFin = $duobac->getPurDateFin();
            $dateDebut = $duobac->getPurDateDebut();
            $contrainteDebut = $contrainteFin = null;

            if ($dateDebut && (int)$dateDebut->format('Y') === $year) {
                $contrainteDebut = $dateDebut;
            }

            if ($dateFin && (int)$dateFin->format('Y') === $year) {
                $contrainteFin = $dateFin;
            }

            $pesees[] = $this->findByPuceAndYear($puce, $year, $contrainteDebut, $contrainteFin);
        }

        return array_merge(...$pesees);
    }

    public function removeByYear(int $year)
    {
        $pesees = $this->createQueryBuilder('pesee')
            ->orderBy('pesee.date_pesee', 'ASC')
            ->andWhere('pesee.date_pesee LIKE :annee')
            ->setParameter('annee', $year."%")
            ->getQuery()
            ->getResult();

        foreach ($pesees as $pesee) {
            $this->remove($pesee);
        }

        $this->flush();
    }
}
