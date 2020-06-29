<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 7/11/18
 * Time: 9:00
 */

namespace AcMarche\Duobac\Security;

class SecurityData
{
    public static function getRoles(): iterable
    {
        $roles = [self::getRoleAdmin(), self::getRoleUser()];

        return array_combine($roles, $roles);
    }

    public static function getRoleAdmin(): string
    {
        return 'ROLE_DUOBAC_ADMIN';
    }

    public static function getRoleUser(): string
    {
        return 'ROLE_DUOBAC';
    }
}
