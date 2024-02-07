<?php

namespace AcMarche\Duobac\Entity;

use AcMarche\Duobac\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, Stringable
{
    use IdTrait;

    #[ORM\Column(nullable: false)]
    public array $roles = [];
    #[ORM\Column(type: 'string')]
    public ?string $password = null;
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    public ?string $nom = null;
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    public ?string $prenom = null;
    #[ORM\Column(type: 'string', length: 15, nullable: false)]
    public ?string $rdv_matricule = null;
    /**
     * @var Duobac[] $duobacs
     */
    #[ORM\OneToMany(targetEntity: Duobac::class, mappedBy: 'user')]
    public Collection $duobacs;

    public ?string $plain_password = null;

    public function __construct()
    {
        $this->duobacs = new ArrayCollection();
    }

    public function __toString(): string
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

    public function getPlainPassword(): ?string
    {
        return $this->plain_password;
    }

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
            if ($duobac->user === $this) {
                $duobac->user = null;
            }
        }

        return $this;
    }
}
