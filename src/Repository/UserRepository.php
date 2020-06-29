<?php

namespace AcMarche\Duobac\Repository;

use AcMarche\Duobac\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function insert(User $user)
    {
        $this->_em->persist($user);
        $this->save();
    }

    public function save()
    {
        $this->_em->flush();
    }

    public function remove(User $user)
    {
        $this->_em->remove($user);
        $this->save();
    }
}
