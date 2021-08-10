<?php

namespace AcMarche\Duobac\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DuobacMoyenne
 *
 * @ORM\Table(name="pesee_moyenne", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"a_charge", "date_pesee"})
 * })
 * @ORM\Entity(repositoryClass="AcMarche\Duobac\Repository\PeseeMoyenneRepository")
 */
class PeseeMoyenne extends AbstractPesee implements PeseeInterface
{

}
