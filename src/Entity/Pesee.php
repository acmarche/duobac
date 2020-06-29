<?php

namespace AcMarche\Duobac\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *
 *
 * @ORM\Table("pesee", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"puc_no_puce", "date_pesee"})
 * })
 * @ORM\Entity(repositoryClass="AcMarche\Duobac\Repository\PeseeRepository")
 *
 */
class Pesee extends AbstractPesee
{
    /**
     * Nombre de colonne max pes1,pes2,...
     */
    public const NOMBRE_PESEE = 80;

    /**
     * @var string
     *
     * @ORM\Column( type="string", length=30, nullable=false)
     */
    private $puc_no_puce;

    /**
     * @var PeseeMoyenne|null $moyenne
     */
    private $moyenne;

    /**
     * @return PeseeMoyenne|null
     */
    public function getMoyenne(): ?PeseeMoyenne
    {
        return $this->moyenne;
    }

    /**
     * @param PeseeMoyenne|null $moyenne
     */
    public function setMoyenne(?PeseeMoyenne $moyenne): void
    {
        $this->moyenne = $moyenne;
    }

    public function __construct(string $pucNoPuce, \DateTimeInterface $date, float $poids, $a_charge)
    {
        $this->puc_no_puce = $pucNoPuce;
        $this->date_pesee = $date;
        $this->poids = $poids;
        $this->a_charge = $a_charge;
    }

    public function __toString()
    {
        return $this->puc_no_puce;
    }

    public function getPucNoPuce(): string
    {
        return $this->puc_no_puce;
    }

    public function setPucNoPuce(string $puc_no_puce): PeseeInterface
    {
        $this->puc_no_puce = $puc_no_puce;

        return $this;
    }
}
