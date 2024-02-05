<?php

namespace AcMarche\Duobac\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait PeseeTrait
{
    #[ORM\Column(type: 'date', nullable: false)]
    public ?DateTimeInterface $date_pesee = null;

    #[ORM\Column(precision: 6, scale: 2, nullable: false)]
    public ?float $poids = null;

    #[ORM\Column(type: 'integer', length: 6, nullable: false)]
    public ?int $a_charge = null;
}
