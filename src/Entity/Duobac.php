<?php

namespace AcMarche\Duobac\Entity;

use DateTimeInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

/**
 * Duobac
 *
 * @ORM\Table("duobac", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"rdv_matricule", "puc_no_puce"})
 * })
 * @ORM\Entity(repositoryClass="AcMarche\Duobac\Repository\DuobacRepository")
 *
 */
class Duobac
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=15, nullable=false)
     */
    private ?string $rdv_matricule;

    /**
     * @ORM\Column(type="string", length=30, nullable=false)
     */
    private ?string $puc_no_puce;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private ?DateTimeInterface $pur_date_debut = null;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTimeInterface $pur_date_fin = null;

    /**
     * @ORM\Column(type="string", length=2, nullable=false)
     */
    private ?string $pur_cod_tarification = null;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $puc_cod_capacite;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private ?string $pur_cod_clef = null;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $puc_cod_dechet;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private ?string $rdv_nom = null;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private ?string $rdv_prenom_1 = null;

    /**
     * @ORM\Column(type="string", length=150, nullable=false)
     */
    private ?string $loc_code_post = null;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private ?int $rue_code_rue = null;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private ?string $rue_lib_1lg = null;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private ?string $adr_numero = null;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private ?string $adr_indice = null;

    /**
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private ?string $adr_boite = null;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private ?int $rdv_cod_redevable = null;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private ?int $rdv_cod_classe = null;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private ?string $puc_no_conteneur = null;

    /**
     * @var Pesee[]
     */
    private Collection $pesees;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="duobacs")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?User $user = null;

    public function __construct(string $rdv_matricule, string $puc_no_puce)
    {
        $this->rdv_matricule = $rdv_matricule;
        $this->puc_no_puce = $puc_no_puce;
        $this->pesees = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->puc_no_puce;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRdvMatricule(): string
    {
        return $this->rdv_matricule;
    }

    public function setRdvMatricule(string $rdv_matricule): self
    {
        $this->rdv_matricule = $rdv_matricule;

        return $this;
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

    public function getPurDateDebut(): DateTime
    {
        return $this->pur_date_debut;
    }

    public function setPurDateDebut(DateTimeInterface $pur_date_debut): self
    {
        $this->pur_date_debut = $pur_date_debut;

        return $this;
    }

    public function getPurDateFin(): ?DateTimeInterface
    {
        return $this->pur_date_fin;
    }

    public function setPurDateFin(?DateTimeInterface $pur_date_fin): self
    {
        $this->pur_date_fin = $pur_date_fin;

        return $this;
    }

    public function getPurCodTarification(): string
    {
        return $this->pur_cod_tarification;
    }

    public function setPurCodTarification(string $pur_cod_tarification): self
    {
        $this->pur_cod_tarification = $pur_cod_tarification;

        return $this;
    }

    public function getPucCodCapacite(): bool
    {
        return $this->puc_cod_capacite;
    }

    public function setPucCodCapacite(?bool $puc_cod_capacite): self
    {
        $this->puc_cod_capacite = $puc_cod_capacite;

        return $this;
    }

    public function getPurCodClef(): string
    {
        return $this->pur_cod_clef;
    }

    public function setPurCodClef(?string $pur_cod_clef): self
    {
        $this->pur_cod_clef = $pur_cod_clef;

        return $this;
    }

    public function getPucCodDechet(): ?bool
    {
        return $this->puc_cod_dechet;
    }

    public function setPucCodDechet(?bool $puc_cod_dechet): self
    {
        $this->puc_cod_dechet = $puc_cod_dechet;

        return $this;
    }

    public function getRdvNom(): string
    {
        return $this->rdv_nom;
    }

    public function setRdvNom(?string $rdv_nom): self
    {
        $this->rdv_nom = $rdv_nom;

        return $this;
    }

    public function getRdvPrenom1(): string
    {
        return $this->rdv_prenom_1;
    }

    public function setRdvPrenom1(?string $rdv_prenom_1): self
    {
        $this->rdv_prenom_1 = $rdv_prenom_1;

        return $this;
    }

    public function getLocCodePost(): string
    {
        return $this->loc_code_post;
    }

    public function setLocCodePost(string $loc_code_post): self
    {
        $this->loc_code_post = $loc_code_post;

        return $this;
    }

    public function getRueCodeRue(): int
    {
        return $this->rue_code_rue;
    }

    public function setRueCodeRue(int $rue_code_rue): self
    {
        $this->rue_code_rue = $rue_code_rue;

        return $this;
    }

    public function getRueLib1lg(): ?string
    {
        return $this->rue_lib_1lg;
    }

    public function setRueLib1lg(?string $rue_lib_1lg): self
    {
        $this->rue_lib_1lg = $rue_lib_1lg;

        return $this;
    }

    public function getAdrNumero(): string
    {
        return $this->adr_numero;
    }

    public function setAdrNumero(?string $adr_numero): self
    {
        $this->adr_numero = $adr_numero;

        return $this;
    }

    public function getAdrIndice(): string
    {
        return $this->adr_indice;
    }

    public function setAdrIndice(?string $adr_indice): self
    {
        $this->adr_indice = $adr_indice;

        return $this;
    }

    public function getAdrBoite(): string
    {
        return $this->adr_boite;
    }

    public function setAdrBoite(?string $adr_boite): self
    {
        $this->adr_boite = $adr_boite;

        return $this;
    }

    public function getRdvCodRedevable(): int
    {
        return $this->rdv_cod_redevable;
    }

    public function setRdvCodRedevable(int $rdv_cod_redevable): self
    {
        $this->rdv_cod_redevable = $rdv_cod_redevable;

        return $this;
    }

    public function getRdvCodClasse(): int
    {
        return $this->rdv_cod_classe;
    }

    public function setRdvCodClasse(int $rdv_cod_classe): self
    {
        $this->rdv_cod_classe = $rdv_cod_classe;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Pesee[]
     */
    public function getPesees(): array
    {
        return $this->pesees;
    }

    public function addPesee(Pesee $pesee): self
    {
        if (!$this->pesees->contains($pesee)) {
            $this->pesees[] = $pesee;
        }

        return $this;
    }

    public function removePesee(Pesee $pesee): self
    {
        if ($this->pesees->contains($pesee)) {
            $this->pesees->removeElement($pesee);
        }

        return $this;
    }

    public function getPucNoConteneur(): string
    {
        return $this->puc_no_conteneur;
    }

    public function setPucNoConteneur(?string $puc_no_conteneur): self
    {
        $this->puc_no_conteneur = $puc_no_conteneur;

        return $this;
    }
}
