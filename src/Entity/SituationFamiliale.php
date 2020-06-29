<?php

namespace AcMarche\Duobac\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Table("situation_familiale", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"rdv_matricule", "annee", "puc_no_puce"})
 * })
 * @ORM\Entity(repositoryClass="AcMarche\Duobac\Repository\SituationFamilialeRepository")
 *
 */
class SituationFamiliale
{
    /**
     * @var integer|null $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=15, nullable=false)
     */
    private $rdv_matricule;

    /**
    * @var string
    *
    * @ORM\Column(type="string", length=30, nullable=false)
    */
    private $puc_no_puce;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=4, nullable=false)
     */
    private $annee;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $a_charge;

    public function __construct(string $rdv_matricule, string $puce, int $annee, int $a_charge)
    {
        $this->rdv_matricule = $rdv_matricule;
        $this->puc_no_puce = $puce;
        $this->annee = $annee;
        $this->a_charge = $a_charge;
    }

    public function __toString()
    {
        return (string)$this->a_charge;
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
