<?php

namespace AcMarche\Duobac\Entity;

use AcMarche\Duobac\Repository\SituationFamilialeRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Table(name: 'situation_familiale')]
//#[ORM\UniqueConstraint(columns: ['rdv_matricule', 'annee', 'puc_no_puce'])]
#[ORM\Entity(repositoryClass: SituationFamilialeRepository::class)]
class SituationFamiliale implements Stringable
{
    use IdTrait;

    public function __construct(
        #[ORM\Column(type: 'string', length: 15, nullable: false)] public ?string $rdv_matricule,
        #[ORM\Column(type: 'string', length: 30, nullable: false)] public ?string $puc_no_puce,
        #[ORM\Column(type: 'integer', length: 4, nullable: false)] public ?int $annee,
        #[ORM\Column(type: 'integer', nullable: false)] public ?int $a_charge
    ) {
    }

    public function __toString(): string
    {
        return (string)$this->a_charge;
    }
}
