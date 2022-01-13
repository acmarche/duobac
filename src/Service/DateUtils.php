<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 15/11/18
 * Time: 14:32.
 */

namespace AcMarche\Duobac\Service;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;

class DateUtils
{
    /**
     * @param string $format
     *
     * @return bool|DateTimeInterface
     *
     * @throws Exception
     */
    public function convertStringToDateTime(string $date, $format = 'd/m/Y'): DateTimeImmutable
    {
        if (!$dateTime = DateTimeImmutable::createFromFormat($format, $date)) {
            throw new Exception("Date $date n'a pas pu être convertie en DateTime, format: ".$format);
        }

        return $dateTime;
    }

    public static function getAllMonths(): array
    {
        return [
            0 => 'Janvier',
            'Février',
            'Mars',
            'Avril',
            'Mai',
            'Juin',
            'Juillet',
            'Août',
            'Septembre',
            'Octobre',
            'Novembre',
            'Décembre',
        ];
    }

    public static function getListeNumeroMoisWith2digits(): array
    {
        $mois = range(1, 12);

        foreach ($mois as $key => $item) {
            $digits2 = sprintf('%02d', $item);
            $mois[$key] = $digits2;
        }

        return $mois;
    }

    public static function getListeNumeroMoisWithOnedigit(): array
    {
        return range(1, 12);
    }
}
