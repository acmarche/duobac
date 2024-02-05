<?php

use AcMarche\Duobac\DoctrineExtensions\Month;
use AcMarche\Duobac\DoctrineExtensions\Year;

use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrine) {
    $em = $doctrine->orm()->entityManager('default');
    $em->connection('default');
    $em->dql(['string_functions' => ['YEAR' => Year::class, 'MONTH' => Month::class]]);

    $em->mapping('CapCommercio')
        ->isBundle(false)
        ->type('attribute')
        ->dir('%kernel.project_dir%/src/AcMarche/Duobac/src/Entity')
        ->prefix('AcMarche\Duobac')
        ->alias('AcMarcheDuobac');
};
