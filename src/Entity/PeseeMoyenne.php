<?php

namespace AcMarche\Duobac\Entity;

use AcMarche\Duobac\Repository\PeseeMoyenneRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * DuobacMoyenne.
 */
#[ORM\Table(name: 'pesee_moyenne')]
#[ORM\UniqueConstraint(columns: ['a_charge', 'date_pesee'])]
#[ORM\Entity(repositoryClass: PeseeMoyenneRepository::class)]
class PeseeMoyenne extends AbstractPesee implements PeseeInterface
{
}
