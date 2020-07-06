<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 17/11/18
 * Time: 12:56
 */

namespace AcMarche\Duobac\Entity;

interface PeseeInterface
{
    public function getDatePesee(): \DateTimeInterface;

    public function getPoids(): float;

    public function setPoids(float $poids): self;

    public function getId(): ?int;

    public function setDatePesee(\DateTimeInterface $date_pesee): self;

    public function getACharge(): ?int;

    public function setACharge(int $a_charge): self;

}
