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

    public static function ensureUtf8(string $text): string
    {
        try {
            if (!mb_check_encoding($text, 'UTF-8')) {
                $text = mb_convert_encoding($text, 'UTF-8', 'auto');
            }
        } catch (\Exception $e) {

        }

        return $text;
    }
}
