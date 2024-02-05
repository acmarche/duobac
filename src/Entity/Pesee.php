<?php

namespace AcMarche\Duobac\Entity;

use AcMarche\Duobac\Repository\PeseeRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Table(name: 'pesee')]
//#[ORM\UniqueConstraint(columns: ['puc_no_puce', 'date_pesee'])]
#[ORM\Entity(repositoryClass: PeseeRepository::class)]
class Pesee implements Stringable, PeseeInterface
{
    use IdTrait, PeseeTrait;

    #[ORM\Column(type: 'string', length: 30, nullable: false)]
    public string $puc_no_puce;

    /**
     * Nombre de colonne max pes1,pes2,...
     */
    public const NOMBRE_PESEE = 80;

    public ?PeseeMoyenne $moyenne = null;

    public function __construct(
        string $puc_no_puce,
        DateTimeInterface $date,
        float $poids,
        int $a_charge
    ) {
        $this->date_pesee = $date;
        $this->poids = $poids;
        $this->a_charge = $a_charge;
        $this->puc_no_puce = $puc_no_puce;
    }

    public function __toString(): string
    {
        return $this->puc_no_puce;
    }
}
