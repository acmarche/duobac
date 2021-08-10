<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 22/08/18
 * Time: 13:17
 */

namespace AcMarche\Duobac\Security\Manager;

use AcMarche\Duobac\Entity\Duobac;
use AcMarche\Duobac\Entity\User;
use AcMarche\Duobac\Repository\UserRepository;
use AcMarche\Duobac\Security\SecurityData;

class UserManager
{
    private UserRepository $userRepository;
    private PasswordManager $passwordManager;

    public function __construct(
        UserRepository $userRepository,
        PasswordManager $passwordManager
    ) {
        $this->userRepository = $userRepository;
        $this->passwordManager = $passwordManager;
    }

    public function newInstance($matricule): User
    {
        $user = new User();
        $user->setRdvMatricule($matricule);

        return $user;
    }

    public function newFromDuobac(Duobac $duobac): User
    {
        $user = $this->newInstance($duobac->getRdvMatricule());
        $this->populateUserFromDuobac($user, $duobac);
        $this->addRoleDuobac($user);
        $this->passwordManager->generateNewPassword($user);
        $this->passwordManager->changePassword($user, $user->getPlainPassword());
        $user->addDuobac($duobac);
        $this->insert($user);

        return $user;
    }

    public function insert(User $user): void
    {
        $this->userRepository->insert($user);
    }

    public function save(): void
    {
        $this->userRepository->save();
    }

    public function delete(User $user): void
    {
        $this->userRepository->remove($user);
        $this->userRepository->flush();
    }

    public function addRoleDuobac(User $user): void
    {
        if (!in_array(SecurityData::getRoleUser(), $user->getRoles())) {
            $user->setRoles([SecurityData::getRoleUser()]);
        }
    }

    public function populateUserFromDuobac(User $user, Duobac $duobac): User
    {
        $user->setNom($duobac->getRdvNom());
        $user->setPrenom($duobac->getRdvPrenom1());

        return $user;
    }

    /**
     * @param string $email
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }
}
