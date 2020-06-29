<?php

namespace AcMarche\Duobac\Entity;

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
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=false)
     */
    private $pur_date_debut;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $pur_date_fin;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=2, nullable=false)
     */
    private $pur_cod_tarification;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $puc_cod_capacite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $pur_cod_clef;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $puc_cod_dechet;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $rdv_nom;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $rdv_prenom_1;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=false)
     */
    private $loc_code_post;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $rue_code_rue;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $rue_lib_1lg;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $adr_numero;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $adr_indice;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $adr_boite;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $rdv_cod_redevable;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $rdv_cod_classe;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $puc_no_conteneur;

    /**
     * @var Pesee[]
     */
    private $pesees;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="AcMarche\Duobac\Entity\User", inversedBy="duobacs")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

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

    public function getRdvMatricule(): ?string
    {
        return $this->rdv_matricule;
    }

    public function setRdvMatricule(string $rdv_matricule): self
    {
        $this->rdv_matricule = $rdv_matricule;

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

    public function getPurDateDebut(): ?\DateTimeInterface
    {
        return $this->pur_date_debut;
    }

    public function setPurDateDebut(\DateTimeInterface $pur_date_debut): self
    {
        $this->pur_date_debut = $pur_date_debut;

        return $this;
    }

    public function getPurDateFin(): ?\DateTimeInterface
    {
        return $this->pur_date_fin;
    }

    public function setPurDateFin(?\DateTimeInterface $pur_date_fin): self
    {
        $this->pur_date_fin = $pur_date_fin;

        return $this;
    }

    public function getPurCodTarification(): ?string
    {
        return $this->pur_cod_tarification;
    }

    public function setPurCodTarification(string $pur_cod_tarification): self
    {
        $this->pur_cod_tarification = $pur_cod_tarification;

        return $this;
    }

    public function getPucCodCapacite(): ?bool
    {
        return $this->puc_cod_capacite;
    }

    public function setPucCodCapacite(?bool $puc_cod_capacite): self
    {
        $this->puc_cod_capacite = $puc_cod_capacite;

        return $this;
    }

    public function getPurCodClef(): ?string
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

    public function getRdvNom(): ?string
    {
        return $this->rdv_nom;
    }

    public function setRdvNom(?string $rdv_nom): self
    {
        $this->rdv_nom = $rdv_nom;

        return $this;
    }

    public function getRdvPrenom1(): ?string
    {
        return $this->rdv_prenom_1;
    }

    public function setRdvPrenom1(?string $rdv_prenom_1): self
    {
        $this->rdv_prenom_1 = $rdv_prenom_1;

        return $this;
    }

    public function getLocCodePost(): ?string
    {
        return $this->loc_code_post;
    }

    public function setLocCodePost(string $loc_code_post): self
    {
        $this->loc_code_post = $loc_code_post;

        return $this;
    }

    public function getRueCodeRue(): ?int
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

    public function getAdrNumero(): ?string
    {
        return $this->adr_numero;
    }

    public function setAdrNumero(?string $adr_numero): self
    {
        $this->adr_numero = $adr_numero;

        return $this;
    }

    public function getAdrIndice(): ?string
    {
        return $this->adr_indice;
    }

    public function setAdrIndice(?string $adr_indice): self
    {
        $this->adr_indice = $adr_indice;

        return $this;
    }

    public function getAdrBoite(): ?string
    {
        return $this->adr_boite;
    }

    public function setAdrBoite(?string $adr_boite): self
    {
        $this->adr_boite = $adr_boite;

        return $this;
    }

    public function getRdvCodRedevable(): ?int
    {
        return $this->rdv_cod_redevable;
    }

    public function setRdvCodRedevable(int $rdv_cod_redevable): self
    {
        $this->rdv_cod_redevable = $rdv_cod_redevable;

        return $this;
    }

    public function getRdvCodClasse(): ?int
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
    public function getPesees(): Collection
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

    public function getPucNoConteneur(): ?string
    {
        return $this->puc_no_conteneur;
    }

    public function setPucNoConteneur(?string $puc_no_conteneur): self
    {
        $this->puc_no_conteneur = $puc_no_conteneur;

        return $this;
    }
}
