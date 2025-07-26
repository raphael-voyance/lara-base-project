<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des Plugins
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient la configuration globale pour la gestion des plugins
    | de l'application Laravel.
    |
    */

    // Répertoire principal des plugins
    'plugins_path' => app_path('Plugins'),

    // Namespace de base pour les plugins
    'namespace' => 'App\\Plugins',

    // Plugins activés
    'enabled' => [],

    // Configuration par défaut des plugins
    'defaults' => [
        'auto_discover' => true,
        'auto_register' => true,
        'publish_assets' => true,
        'load_migrations' => true,
        'load_routes' => true,
        'load_views' => true,
        'load_translations' => true,
    ],

    // Configuration des assets
    'assets' => [
        'publish_path' => public_path('vendor/plugins'),
        'auto_compile' => true,
        'version' => true,
    ],

    // Configuration du cache
    'cache' => [
        'enabled' => env('PLUGINS_CACHE', true),
        'key' => 'plugins.manifest',
        'ttl' => 3600, // 1 heure
    ],

    // Configuration des permissions
    'permissions' => [
        'auto_register' => true,
        'namespace' => 'plugins',
    ],

    // Configuration des événements
    'events' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des commandes Artisan
    'commands' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des middlewares
    'middleware' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des validations
    'validations' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des helpers
    'helpers' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des macros
    'macros' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des composants Blade
    'blade_components' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
        'prefix' => 'plugin',
    ],

    // Configuration des directives Blade
    'blade_directives' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des facades
    'facades' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des services
    'services' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des repositories
    'repositories' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des policies
    'policies' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des observers
    'observers' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des listeners
    'listeners' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des notifications
    'notifications' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des jobs
    'jobs' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des exceptions
    'exceptions' => [
        'auto_register' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration des tests
    'tests' => [
        'auto_discover' => true,
        'namespace' => 'App\\Plugins',
    ],

    // Configuration de la documentation
    'documentation' => [
        'auto_generate' => true,
        'path' => base_path('docs/plugins'),
    ],

    // Configuration des mises à jour
    'updates' => [
        'auto_check' => true,
        'check_interval' => 86400, // 24 heures
    ],

    // Configuration de la sécurité
    'security' => [
        'validate_signatures' => true,
        'allowed_domains' => [],
        'max_file_size' => 10485760, // 10 MB
    ],
];
