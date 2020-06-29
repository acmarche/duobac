<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 15/11/18
 * Time: 13:39
 */

namespace AcMarche\Duobac\Manager;

use AcMarche\Duobac\Entity\Duobac;
use AcMarche\Duobac\Repository\DuobacRepository;
use AcMarche\Duobac\Service\DateUtils;
use AcMarche\Security\Entity\User;

class DuobacManager
{
    /**
     * @var DuobacRepository
     */
    private $duobacRepository;
    /**
     * @var DateUtils
     */
    private $dateUtils;

    public function __construct(DuobacRepository $duobacRepository, DateUtils $dateUtils)
    {
        $this->duobacRepository = $duobacRepository;
        $this->dateUtils = $dateUtils;
    }

    public function getInstance(string $rdv_matricule, string $puc_no_puce): Duobac
    {
        if (!$duobac = $this->duobacRepository->findOneBy(
            ['rdv_matricule' => $rdv_matricule, 'puc_no_puce' => $puc_no_puce]
        )) {
            $duobac = new Duobac($rdv_matricule, $puc_no_puce);
            $this->duobacRepository->persist($duobac);
        }

        return $duobac;
    }

    /**
     *
     * @return Duobac
     */
    public function getDuobacByMatriculeAndPuce(string $matricule, string $puce): ?Duobac
    {
        return $this->duobacRepository->findOneBy(['rdv_matricule' => $matricule, 'puc_no_puce' => $puce]);
    }

    /**
     * @param User $user
     * @return Duobac[]
     */
    public function getDuobacsByUser(User $user)
    {
        return $this->duobacRepository->findBy(['rdv_matricule' => $user->getRdvMatricule()]);
    }

    /**
     * @return Duobac[]
     */
    public function getDuobacsCitoyens()
    {
        $data = $this->duobacRepository->findAll();
        $duobacs = [];
        foreach ($data as $duobac) {
            if (strlen($duobac->getRdvMatricule()) == 11) {
                $duobacs[] = $duobac;
            }
        }

        return $duobacs;
    }

    /**
     * @param Duobac[] $duobacs
     * @return array
     */
    public function getPucesCitoyensByDuobacs(array $duobacs)
    {
        $puces = [];
        foreach ($duobacs as $duobac) {
            if (strlen($duobac->getRdvMatricule()) === 11) {
                $puces[] = $duobac->getPucNoPuce();
            }
        }

        return $puces;
    }

    public function flush()
    {
        $this->duobacRepository->flush();
    }

    public function persist(Duobac $duobac)
    {
        $this->duobacRepository->persist($duobac);
    }
}
