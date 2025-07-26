<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dépendances Composer du Plugin Task Manager
    |--------------------------------------------------------------------------
    |
    | Ce fichier définit toutes les dépendances Composer requises
    | pour le bon fonctionnement du plugin Task Manager.
    |
    */

    'required' => [
        /*
        |--------------------------------------------------------------------------
        | Dépendances principales
        |--------------------------------------------------------------------------
        */
        'livewire/livewire' => [
            'version' => '^3.0',
            'description' => 'Composants interactifs pour Laravel',
            'required' => true,
        ],

        'spatie/laravel-permission' => [
            'version' => '^5.0',
            'description' => 'Gestion des permissions et rôles',
            'required' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Dépendances pour les fonctionnalités avancées
        |--------------------------------------------------------------------------
        */
        'spatie/laravel-activitylog' => [
            'version' => '^4.0',
            'description' => 'Journalisation des activités',
            'required' => false,
        ],

        'spatie/laravel-medialibrary' => [
            'version' => '^10.0',
            'description' => 'Gestion des fichiers médias',
            'required' => false,
        ],

        'maatwebsite/excel' => [
            'version' => '^3.1.48',
            'description' => 'Export Excel des données',
            'required' => false,
        ],

        'barryvdh/laravel-dompdf' => [
            'version' => '^2.0',
            'description' => 'Génération de PDF',
            'required' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | Dépendances pour les notifications
        |--------------------------------------------------------------------------
        */
        'spatie/laravel-notification-log' => [
            'version' => '^1.0',
            'description' => 'Journalisation des notifications',
            'required' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | Dépendances pour l'API
        |--------------------------------------------------------------------------
        */
        'laravel/sanctum' => [
            'version' => '^3.0',
            'description' => 'Authentification API',
            'required' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | Dépendances pour le cache
        |--------------------------------------------------------------------------
        */
        'predis/predis' => [
            'version' => '^2.0',
            'description' => 'Client Redis pour le cache',
            'required' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | Dépendances pour les tests
        |--------------------------------------------------------------------------
        */
        'fakerphp/faker' => [
            'version' => '^1.20',
            'description' => 'Génération de données de test',
            'required' => false,
            'dev' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration d'installation
    |--------------------------------------------------------------------------
    */
    'install_config' => [
        'auto_install' => true,
        'skip_optional' => false,
        'confirm_each' => false,
        'timeout' => 300, // 5 minutes
        'memory_limit' => '512M',
    ],

    /*
    |--------------------------------------------------------------------------
    | Scripts post-installation
    |--------------------------------------------------------------------------
    */
    'post_install_scripts' => [
        'spatie/laravel-permission' => [
            'php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"',
            'php artisan migrate',
        ],
        'spatie/laravel-activitylog' => [
            'php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"',
            'php artisan migrate',
        ],
        'spatie/laravel-medialibrary' => [
            'php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="media-library-migrations"',
            'php artisan migrate',
        ],
        'maatwebsite/excel' => [
            'php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config',
        ],
        'barryvdh/laravel-dompdf' => [
            'php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Vérifications de compatibilité
    |--------------------------------------------------------------------------
    */
    'compatibility' => [
        'php' => '^8.1',
        'laravel' => '^10.0',
        'extensions' => [
            'json',
            'pdo',
            'mbstring',
            'xml',
            'curl',
        ],
    ],
];
