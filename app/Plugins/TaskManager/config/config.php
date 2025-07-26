<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration du Plugin Task Manager
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient toutes les configurations nécessaires au bon
    | fonctionnement du plugin de gestion de tâches.
    |
    */

    // Informations du plugin
    'info' => [
        'name' => 'Task Manager',
        'description' => 'Plugin de gestion de tâches avec interface moderne',
        'version' => '1.0.0',
        'author' => 'Votre Nom',
        'website' => 'https://votre-site.com',
        'license' => 'MIT',
    ],

    // Nom du plugin affiché dans l'interface
    'display_name' => env('TASK_MANAGER_NAME', 'Gestionnaire de Tâches'),

    // Préfixe des routes du plugin
    'route_prefix' => env('TASK_MANAGER_ROUTE_PREFIX', 'tasks'),

    // Middleware appliqué aux routes du plugin
    'middleware' => ['web', 'auth'],

    // Configuration de la pagination
    'pagination' => [
        'per_page' => env('TASK_MANAGER_PER_PAGE', 15),
        'max_per_page' => env('TASK_MANAGER_MAX_PER_PAGE', 100),
    ],

    // Configuration des statuts de tâches
    'statuses' => [
        'pending' => [
            'name' => 'En attente',
            'color' => 'yellow',
            'icon' => 'clock',
            'description' => 'Tâche en attente de traitement',
        ],
        'in_progress' => [
            'name' => 'En cours',
            'color' => 'blue',
            'icon' => 'play',
            'description' => 'Tâche en cours de traitement',
        ],
        'completed' => [
            'name' => 'Terminée',
            'color' => 'green',
            'icon' => 'check',
            'description' => 'Tâche terminée avec succès',
        ],
        'cancelled' => [
            'name' => 'Annulée',
            'color' => 'red',
            'icon' => 'x',
            'description' => 'Tâche annulée',
        ],
    ],

    // Configuration des priorités
    'priorities' => [
        'low' => [
            'name' => 'Faible',
            'color' => 'gray',
            'value' => 1,
            'description' => 'Priorité faible',
        ],
        'medium' => [
            'name' => 'Moyenne',
            'color' => 'yellow',
            'value' => 2,
            'description' => 'Priorité moyenne',
        ],
        'high' => [
            'name' => 'Élevée',
            'color' => 'orange',
            'value' => 3,
            'description' => 'Priorité élevée',
        ],
        'urgent' => [
            'name' => 'Urgente',
            'color' => 'red',
            'value' => 4,
            'description' => 'Priorité urgente',
        ],
    ],

    // Configuration des notifications
    'notifications' => [
        'enabled' => env('TASK_MANAGER_NOTIFICATIONS', true),
        'channels' => ['mail', 'database'],
        'reminder_days' => env('TASK_MANAGER_REMINDER_DAYS', 1),
        'due_date_reminder' => env('TASK_MANAGER_DUE_DATE_REMINDER', true),
        'assignment_notification' => env('TASK_MANAGER_ASSIGNMENT_NOTIFICATION', true),
        'completion_notification' => env('TASK_MANAGER_COMPLETION_NOTIFICATION', true),
    ],

    // Configuration des permissions
    'permissions' => [
        'view_tasks' => 'Voir les tâches',
        'create_tasks' => 'Créer des tâches',
        'edit_tasks' => 'Modifier les tâches',
        'delete_tasks' => 'Supprimer les tâches',
        'assign_tasks' => 'Assigner des tâches',
        'manage_categories' => 'Gérer les catégories',
        'view_reports' => 'Voir les rapports',
        'export_tasks' => 'Exporter les tâches',
    ],

    // Configuration de l'interface utilisateur
    'ui' => [
        'theme' => env('TASK_MANAGER_THEME', 'default'),
        'show_sidebar' => env('TASK_MANAGER_SHOW_SIDEBAR', true),
        'enable_dark_mode' => env('TASK_MANAGER_DARK_MODE', true),
        'enable_animations' => env('TASK_MANAGER_ANIMATIONS', true),
        'show_progress_bars' => env('TASK_MANAGER_PROGRESS_BARS', true),
        'show_task_counters' => env('TASK_MANAGER_TASK_COUNTERS', true),
        'enable_drag_drop' => env('TASK_MANAGER_DRAG_DROP', true),
    ],

    // Configuration des exports
    'exports' => [
        'enabled' => env('TASK_MANAGER_EXPORTS', true),
        'formats' => ['csv', 'xlsx', 'pdf'],
        'max_records' => env('TASK_MANAGER_MAX_EXPORT_RECORDS', 1000),
        'include_attachments' => env('TASK_MANAGER_EXPORT_ATTACHMENTS', false),
    ],

    // Configuration du cache
    'cache' => [
        'enabled' => env('TASK_MANAGER_CACHE', true),
        'ttl' => env('TASK_MANAGER_CACHE_TTL', 3600), // 1 heure
        'tags' => ['task-manager'],
        'cache_dashboard' => env('TASK_MANAGER_CACHE_DASHBOARD', true),
        'cache_reports' => env('TASK_MANAGER_CACHE_REPORTS', true),
    ],

    // Configuration des fichiers
    'files' => [
        'max_size' => env('TASK_MANAGER_MAX_FILE_SIZE', 10485760), // 10 MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'],
        'storage_disk' => env('TASK_MANAGER_STORAGE_DISK', 'local'),
        'enable_thumbnails' => env('TASK_MANAGER_THUMBNAILS', true),
    ],

    // Configuration des commentaires
    'comments' => [
        'enabled' => env('TASK_MANAGER_COMMENTS', true),
        'max_length' => env('TASK_MANAGER_COMMENT_MAX_LENGTH', 1000),
        'enable_rich_text' => env('TASK_MANAGER_RICH_TEXT_COMMENTS', true),
        'enable_mentions' => env('TASK_MANAGER_COMMENT_MENTIONS', true),
    ],

    // Configuration des rapports
    'reports' => [
        'enabled' => env('TASK_MANAGER_REPORTS', true),
        'auto_generate' => env('TASK_MANAGER_AUTO_REPORTS', false),
        'schedule' => env('TASK_MANAGER_REPORT_SCHEDULE', 'weekly'),
        'include_charts' => env('TASK_MANAGER_REPORT_CHARTS', true),
    ],

    // Configuration des intégrations
    'integrations' => [
        'calendar' => [
            'enabled' => env('TASK_MANAGER_CALENDAR_INTEGRATION', false),
            'provider' => env('TASK_MANAGER_CALENDAR_PROVIDER', 'google'),
        ],
        'slack' => [
            'enabled' => env('TASK_MANAGER_SLACK_INTEGRATION', false),
            'webhook_url' => env('TASK_MANAGER_SLACK_WEBHOOK'),
        ],
        'email' => [
            'enabled' => env('TASK_MANAGER_EMAIL_INTEGRATION', true),
            'template' => env('TASK_MANAGER_EMAIL_TEMPLATE', 'task-manager::emails.task'),
        ],
    ],

    // Configuration de la sécurité
    'security' => [
        'enable_audit_log' => env('TASK_MANAGER_AUDIT_LOG', true),
        'log_changes' => env('TASK_MANAGER_LOG_CHANGES', true),
        'require_approval' => env('TASK_MANAGER_REQUIRE_APPROVAL', false),
        'max_login_attempts' => env('TASK_MANAGER_MAX_LOGIN_ATTEMPTS', 5),
    ],

    // Configuration des webhooks
    'webhooks' => [
        'enabled' => env('TASK_MANAGER_WEBHOOKS', false),
        'endpoints' => [
            'task_created' => env('TASK_MANAGER_WEBHOOK_TASK_CREATED'),
            'task_updated' => env('TASK_MANAGER_WEBHOOK_TASK_UPDATED'),
            'task_completed' => env('TASK_MANAGER_WEBHOOK_TASK_COMPLETED'),
        ],
    ],
];
