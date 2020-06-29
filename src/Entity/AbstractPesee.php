<?php

namespace AcMarche\Duobac\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *
 *
 */
abstract class AbstractPesee implements PeseeInterface
{
    /**
     * @var integer|null $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     */
    protected $id;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="date", nullable=false)
     */
    protected $date_pesee;

    /**
     * @var float|null
     *
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=false)
     */
    protected $poids;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=6, nullable=false)
     */
    protected $a_charge;

    public function __get($prop)
    {
        return $this->$prop;
    }

    public function __isset($prop): bool
    {
        return isset($this->$prop);
    }

    public function getDatePesee(): \DateTimeInterface
    {
        return $this->date_pesee;
    }

    public function getPoids(): float
    {
        return $this->poids;
    }

    public function setPoids(float $poids): PeseeInterface
    {
        $this->poids = $poids;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDatePesee(\DateTimeInterface $date_pesee): PeseeInterface
    {
        $this->date_pesee = $date_pesee;

        return $this;
    }

    public function getACharge(): ?int
    {
        return $this->a_charge;
    }

    public function setACharge(int $a_charge): PeseeInterface
    {
        $this->a_charge = $a_charge;

        return $this;
    }
}
