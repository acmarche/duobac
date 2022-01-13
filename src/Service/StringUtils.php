<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 16/11/18
 * Time: 9:53.
 */

namespace AcMarche\Duobac\Service;

class StringUtils
{
    public static function removeChars(string $string): ?string
    {
        return preg_replace('/[^0-9]/', '', $string);
    }
}
