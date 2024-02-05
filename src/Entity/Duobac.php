<?php

namespace AcMarche\Duobac\Entity;

use AcMarche\Duobac\Repository\DuobacRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'duobac')]
//#[ORM\UniqueConstraint(columns: ['rdv_matricule', 'puc_no_puce'])]
#[ORM\Entity(repositoryClass: DuobacRepository::class)]
class Duobac
{
    use IdTrait;

    #[ORM\Column(length: 15, nullable: false)]
    public ?string $rdv_matricule;

    #[ORM\Column(length: 30, nullable: false)]
    public ?string $puc_no_puce;

    #[ORM\Column(type: 'date', nullable: false)]
    public ?\DateTimeInterface $pur_date_debut = null;
    #[ORM\Column(type: 'date', nullable: true)]
    public ?\DateTimeInterface $pur_date_fin = null;
    #[ORM\Column(length: 2, nullable: false)]
    public ?string $pur_cod_tarification = null;
    #[ORM\Column(type: 'boolean', nullable: true)]
    public bool $puc_cod_capacite;
    #[ORM\Column(length: 5, nullable: true)]
    public ?string $pur_cod_clef = null;
    #[ORM\Column(type: 'boolean', nullable: true)]
    public bool|null $puc_cod_dechet;
    #[ORM\Column(length: 250, nullable: true)]
    public ?string $rdv_nom = null;
    #[ORM\Column(length: 150, nullable: true)]
    public ?string $rdv_prenom_1 = null;
    #[ORM\Column(length: 150, nullable: false)]
    public ?string $loc_code_post = null;
    #[ORM\Column(type: 'integer', nullable: false)]
    public ?int $rue_code_rue = null;
    #[ORM\Column(length: 250, nullable: true)]
    public ?string $rue_lib_1lg = null;
    #[ORM\Column(length: 12, nullable: true)]
    public ?string $adr_numero = null;
    #[ORM\Column(length: 12, nullable: true)]
    public ?string $adr_indice = null;
    #[ORM\Column(length: 24, nullable: true)]
    public ?string $adr_boite = null;
    #[ORM\Column(type: 'integer', nullable: false)]
    public ?int $rdv_cod_redevable = null;
    #[ORM\Column(type: 'integer', nullable: false)]
    public ?int $rdv_cod_classe = null;
    #[ORM\Column(length: 20, nullable: true)]
    public ?string $puc_no_conteneur = null;

    /**
     * @var Pesee[]
     */
    public Collection $pesees;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'duobacs')]
    #[ORM\JoinColumn(nullable: true)]
    public ?User $user = null;

    public function __construct(string $rdv_matricule, string $puc_no_puce)
    {
        $this->pesees = new ArrayCollection();
        $this->rdv_matricule = $rdv_matricule;
        $this->puc_no_puce = $puc_no_puce;
    }

    public function __toString(): string
    {
        return (string)$this->puc_no_puce;
    }
}