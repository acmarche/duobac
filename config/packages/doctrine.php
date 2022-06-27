<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'doctrine',
        [
            'orm' => [
                'dql' => [
                    'datetime_functions' => [
                        'month' => 'DoctrineExtensions\Query\Mysql\Month',
                        'year' => 'DoctrineExtensions\Query\Mysql\Year',
                    ],
                ],
                'mappings' => [
                    'AcMarche\Duobac' => [
                        'is_bundle' => false,
                        'dir' => '%kernel.project_dir%/src/AcMarche/Duobac/src/Entity',
                        'prefix' => 'AcMarche\Duobac',
                        'alias' => 'AcMarche\Duobac',
                    ],
                ],
            ],
        ]
    );
};
