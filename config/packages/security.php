<?php

use AcMarche\Duobac\Entity\User;
use AcMarche\Duobac\Security\Authenticator\DuobacAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            User::class => [
                'algorithm' => 'auto',
            ],
        ],
    ]);

    $containerConfigurator->extension(
        'security',
        [
            'providers' => [
                'duobac_user_provider' => [
                    'entity' => [
                        'class' => User::class,
                        'property' => 'rdv_matricule',
                    ],
                ],
            ],
        ]
    );

    $authenticators = [DuobacAuthenticator::class];

    $main = [
        'provider' => 'duobac_user_provider',
        'logout' => [
            'path' => 'app_logout',
        ],
        'form_login' => [],
        'entry_point' => DuobacAuthenticator::class,
        'switch_user' => true,
    ];

    $main['custom_authenticator'] = $authenticators;

    $containerConfigurator->extension(
        'security',
        [
            'firewalls' => [
                'main' => $main,
            ],
        ]
    );
};
