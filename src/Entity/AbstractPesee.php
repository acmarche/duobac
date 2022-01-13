<?php

namespace AcMarche\Duobac\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

abstract class AbstractPesee implements PeseeInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected ?int $id = null;

    #[ORM\Column(type: 'date', nullable: false)]
    protected ?DateTimeInterface $date_pesee = null;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2, nullable: false)]
    protected ?float $poids = null;

    #[ORM\Column(type: 'integer', length: 6, nullable: false)]
    protected ?int $a_charge = null;

    public function __get($prop)
    {
        return $this->$prop;
    }

    public function __isset($prop): bool
    {
        return isset($this->$prop);
    }

    public function getDatePesee(): DateTimeInterface
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

    public function setDatePesee(DateTimeInterface $date_pesee): PeseeInterface
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
