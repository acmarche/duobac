<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/11/18
 * Time: 12:03
 */

namespace AcMarche\Duobac\Security\Manager;

use AcMarche\Duobac\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordManager
{
    private UserPasswordHasherInterface $userPasswordEncoder;

    public function __construct(
        UserPasswordHasherInterface $userPasswordEncoder
    ) {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function generateNewPassword(User $user): void
    {
        $password = $this->generatePassword();
        $user->setPlainPassword($password);
    }

    public function changePassword(User $user, string $plainPassword): void
    {
        $passwordCrypted = $this->userPasswordEncoder->hashPassword($user, $plainPassword);
        $user->setPassword($passwordCrypted);
        $user->setPlainPassword($plainPassword);//pour envoie par mail
    }

    public function generatePassword(): string
    {
        $password = '';

        for ($i = 0; $i < 6; $i++) {
            $password .= rand(1, 9);
        }

        return $password;
    }
}
