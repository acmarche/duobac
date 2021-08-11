<?php

namespace AcMarche\Duobac\Entity;

use AcMarche\Duobac\Entity\Duobac;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Duobac\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @var iterable $roles
     * @ORM\Column(type="array", nullable=true)
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private ?string $password = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $nom = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $prenom = null;

    /**
     * @ORM\Column(type="string", length=15, nullable=false)
     */
    private ?string $rdv_matricule = null;

    /**
     * @var Duobac[]
     *
     * @ORM\OneToMany(targetEntity=Duobac::class, mappedBy="user")
     */
    private Collection $duobacs;

    private ?string $plain_password = null;

    public function __construct()
    {
        $this->duobacs = new ArrayCollection();
    }

    public function __toString()
    {
        return (string)$this->rdv_matricule;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->rdv_matricule;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->rdv_matricule;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plain_password;
    }

    /**
     * @param null|string $plain_password
     */
    public function setPlainPassword(?string $plain_password): void
    {
        $this->plain_password = $plain_password;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): void
    {
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
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

    /**
     * @return Collection|Duobac[]
     */
    public function getDuobacs(): array
    {
        return $this->duobacs;
    }

    public function addDuobac(Duobac $duobac): self
    {
        if (!$this->duobacs->contains($duobac)) {
            $this->duobacs[] = $duobac;
            $duobac->setUser($this);
        }

        return $this;
    }

    public function removeDuobac(Duobac $duobac): self
    {
        if ($this->duobacs->contains($duobac)) {
            $this->duobacs->removeElement($duobac);
            // set the owning side to null (unless already changed)
            if ($duobac->getUser() === $this) {
                $duobac->setUser(null);
            }
        }

        return $this;
    }
}
