<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'twig',
        [
            'form_themes' => ['bootstrap_5_layout.html.twig'],
            'paths' => [
                '%kernel.project_dir%/src/AcMarche/Duobac/templates' => 'AcMarcheDuobac',
            ],
            'globals' => [
                'bootcdn' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css',
            ],
        ]
    );
};
