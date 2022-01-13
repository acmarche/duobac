<?php

namespace AcMarche\Duobac\Entity;

use AcMarche\Duobac\Repository\PeseeRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Table(name: 'pesee')]
#[ORM\UniqueConstraint(columns: ['puc_no_puce', 'date_pesee'])]
#[ORM\Entity(repositoryClass: PeseeRepository::class)]
class Pesee extends AbstractPesee implements Stringable
{
    /**
     * Nombre de colonne max pes1,pes2,...
     */
    public const NOMBRE_PESEE = 80;
    private ?PeseeMoyenne $moyenne = null;

    public function getMoyenne(): ?PeseeMoyenne
    {
        return $this->moyenne;
    }

    public function setMoyenne(?PeseeMoyenne $moyenne): void
    {
        $this->moyenne = $moyenne;
    }

    public function __construct(#[ORM\Column(type: 'string', length: 30, nullable: false)] private string $puc_no_puce, DateTimeInterface $date, float $poids, $a_charge)
    {
        $this->date_pesee = $date;
        $this->poids = $poids;
        $this->a_charge = $a_charge;
    }

    public function __toString(): string
    {
        return $this->puc_no_puce;
    }

    public function getPucNoPuce(): string
    {
        return $this->puc_no_puce;
    }

    public function setPucNoPuce(string $puc_no_puce): self
    {
        $this->puc_no_puce = $puc_no_puce;

        return $this;
    }
}
