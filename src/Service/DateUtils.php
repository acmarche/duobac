<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 15/11/18
 * Time: 14:32
 */

namespace AcMarche\Duobac\Service;

use DateTimeInterface;
use Exception;
use DateTimeImmutable;

class DateUtils
{
    /**
     * @param string $date
     * @param string $format
     * @return bool|DateTimeInterface
     * @throws Exception
     */
    public function convertStringToDateTime(string $date, $format = 'd/m/Y'): DateTimeImmutable
    {
        if (!$dateTime = DateTimeImmutable::createFromFormat($format, $date)) {
            throw new Exception("Date $date n'a pas pu être convertie en DateTime, format: ".$format);
        }

        return $dateTime;
    }

    public static function getAllMonths()
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

    public static function getTitleMonth($numMonth)
    {
        $months = self::getAllMonths();

        return isset($months[$numMonth]) ? $months[$numMonth] : $numMonth;
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

    public static function getNumeroMois(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('m');
    }
}
