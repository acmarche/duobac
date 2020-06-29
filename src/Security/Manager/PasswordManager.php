<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/11/18
 * Time: 12:03
 */

namespace AcMarche\Duobac\Security\Manager;

use AcMarche\Duobac\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordManager
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function generateNewPassword(User $user)
    {
        $password = $this->generatePassword();
        $user->setPlainPassword($password);
    }

    public function changePassword(User $user, string $plainPassword)
    {
        $passwordCrypted = $this->userPasswordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($passwordCrypted);
        $user->setPlainPassword($plainPassword);//pour envoie par mail
    }

    public function generatePassword()
    {
        $password = '';

        for ($i = 0; $i < 6; $i++) {
            $password .= rand(1, 9);
        }

        return $password;
    }
}
