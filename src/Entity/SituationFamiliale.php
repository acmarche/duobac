<?php

namespace AcMarche\Duobac\Entity;

use AcMarche\Duobac\Repository\SituationFamilialeRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Table(name: 'situation_familiale')]
#[ORM\UniqueConstraint(columns: ['rdv_matricule', 'annee', 'puc_no_puce'])]
#[ORM\Entity(repositoryClass: SituationFamilialeRepository::class)]
class SituationFamiliale implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    public function __construct(#[ORM\Column(type: 'string', length: 15, nullable: false)] private ?string $rdv_matricule, #[ORM\Column(type: 'string', length: 30, nullable: false)] private ?string $puc_no_puce, #[ORM\Column(type: 'integer', length: 4, nullable: false)] private ?int $annee, #[ORM\Column(type: 'integer', nullable: false)] private ?int $a_charge)
    {
    }

    public function __toString(): string
    {
        return (string) $this->a_charge;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRdvMatricule(): ?string
    {
        return $this->rdv_matricule;
    }

    public function setRdvMatricule(string $rdv_matricule): self
    {
        $this->rdv_matricule = $rdv_matricule;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getACharge(): ?int
    {
        return $this->a_charge;
    }

    public function setACharge(int $a_charge): self
    {
        $this->a_charge = $a_charge;

        return $this;
    }

    public function getPucNoPuce(): ?string
    {
        return $this->puc_no_puce;
    }

    public function setPucNoPuce(string $puc_no_puce): self
    {
        $this->puc_no_puce = $puc_no_puce;

        return $this;
    }
}
