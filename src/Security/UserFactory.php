<?php

namespace AcMarche\Duobac\Security;

use AcMarche\Duobac\Entity\Duobac;
use AcMarche\Duobac\Entity\User;
use AcMarche\Duobac\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly UserRepository $userRepository
    ) {
    }

    public function create(Duobac $duobac): User
    {
        $user = new User();
        $user->rdv_matricule = $duobac->rdv_matricule;
        $user->nom = $duobac->rdv_nom;
        $user->prenom = $duobac->rdv_prenom_1;
        if (!\in_array(SecurityData::getRoleUser(), $user->getRoles())) {
            $user->setRoles([SecurityData::getRoleUser()]);
        }
        $password = $this->generatePassword();
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        $user->addDuobac($duobac);
        $this->userRepository->persist($user);
        $this->userRepository->flush();

        return $user;
    }

    private function generatePassword(): string
    {
        $password = '';

        for ($i = 0; $i < 6; ++$i) {
            $password .= random_int(1, 9);
        }

        return $password;
    }
}
