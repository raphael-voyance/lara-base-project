<?php

namespace App\Plugins\TaskManager\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

/**
 * Commande de gestion des permissions pour Task Manager
 *
 * Cette commande permet de :
 * - Créer les permissions nécessaires
 * - Créer le rôle task-manager
 * - Assigner le rôle à des utilisateurs
 * - Vérifier l'état des permissions
 */
class TaskManagerPermissionsCommand extends Command
{
    /**
     * Le nom et la signature de la commande.
     */
    protected $signature = 'task-manager:permissions
                            {action=setup : Action à effectuer (setup, check, assign, list)}
                            {--user= : ID ou email de l\'utilisateur pour l\'action assign}
                            {--role=task-manager : Nom du rôle à assigner}
                            {--force : Forcer la création sans confirmation}';

    /**
     * La description de la commande.
     */
    protected $description = 'Gère les permissions du plugin Task Manager';

    /**
     * Les permissions requises pour le plugin.
     */
    protected array $permissions = [
        'view_tasks' => 'Voir les tâches',
        'create_tasks' => 'Créer des tâches',
        'edit_tasks' => 'Modifier les tâches',
        'delete_tasks' => 'Supprimer des tâches',
        'assign_tasks' => 'Assigner des tâches',
        'export_tasks' => 'Exporter des tâches',
        'manage_categories' => 'Gérer les catégories',
        'view_reports' => 'Voir les rapports',
    ];

    /**
     * Exécute la commande.
     */
    public function handle(): int
    {
        // Vérifier si spatie/laravel-permission est installé
        if (!class_exists('Spatie\Permission\PermissionServiceProvider')) {
            $this->error('❌ Package spatie/laravel-permission non installé.');
            $this->line('Installez-le avec : composer require spatie/laravel-permission');
            return self::FAILURE;
        }

        $action = $this->argument('action');

        return match ($action) {
            'setup' => $this->setupPermissions(),
            'check' => $this->checkPermissions(),
            'assign' => $this->assignRole(),
            'list' => $this->listPermissions(),
            default => $this->showHelp(),
        };
    }

    /**
     * Configure les permissions de base.
     */
    protected function setupPermissions(): int
    {
        $this->info('🔐 Configuration des permissions Task Manager...');

        try {
            // 1. Créer les permissions
            $this->info('📝 Création des permissions...');
            $createdPermissions = [];

            foreach ($this->permissions as $permission => $description) {
                $perm = Permission::firstOrCreate(['name' => $permission]);
                if ($perm->wasRecentlyCreated) {
                    $createdPermissions[] = $permission;
                }
            }

            if (!empty($createdPermissions)) {
                $this->info('✅ Permissions créées : ' . implode(', ', $createdPermissions));
            } else {
                $this->info('ℹ️  Toutes les permissions existent déjà.');
            }

            // 2. Créer le rôle task-manager
            $this->info('👥 Création du rôle task-manager...');
            $role = Role::firstOrCreate(['name' => 'task-manager']);
            $role->syncPermissions(array_keys($this->permissions));

            $this->info('✅ Rôle task-manager créé avec toutes les permissions.');

            // 3. Afficher un résumé
            $this->displayPermissionsSummary();

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la configuration des permissions : ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Vérifie l'état des permissions.
     */
    protected function checkPermissions(): int
    {
        $this->info('🔍 Vérification de l\'état des permissions...');

        // Vérifier les permissions
        $permissionsStatus = [];
        foreach (array_keys($this->permissions) as $permission) {
            $exists = Permission::where('name', $permission)->exists();
            $permissionsStatus[] = [
                'Permission' => $permission,
                'Description' => $this->permissions[$permission],
                'Statut' => $exists ? '✅ Existe' : '❌ Manquante',
            ];
        }

        $this->table(['Permission', 'Description', 'Statut'], $permissionsStatus);

        // Vérifier le rôle
        $role = Role::where('name', 'task-manager')->first();
        if ($role) {
            $this->info('✅ Rôle task-manager existe avec ' . $role->permissions->count() . ' permissions.');
        } else {
            $this->warn('⚠️  Rôle task-manager manquant.');
        }

        // Vérifier les utilisateurs avec le rôle
        $usersWithRole = User::role('task-manager')->count();
        $this->info("👥 {$usersWithRole} utilisateur(s) ont le rôle task-manager.");

        return self::SUCCESS;
    }

    /**
     * Assigne le rôle à un utilisateur.
     */
    protected function assignRole(): int
    {
        $userIdentifier = $this->option('user');
        $roleName = $this->option('role');

        if (!$userIdentifier) {
            $this->error('❌ Veuillez spécifier un utilisateur avec --user=ID ou --user=email');
            return self::FAILURE;
        }

        // Trouver l'utilisateur
        $user = is_numeric($userIdentifier)
            ? User::find($userIdentifier)
            : User::where('email', $userIdentifier)->first();

        if (!$user) {
            $this->error("❌ Utilisateur non trouvé : {$userIdentifier}");
            return self::FAILURE;
        }

        // Vérifier que le rôle existe
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("❌ Rôle non trouvé : {$roleName}");
            $this->line('Créez d\'abord les permissions avec : php artisan task-manager:permissions setup');
            return self::FAILURE;
        }

        // Assigner le rôle
        if ($user->hasRole($roleName)) {
            $this->warn("⚠️  L'utilisateur {$user->name} a déjà le rôle {$roleName}.");

            if (!$this->option('force') && !$this->confirm('Voulez-vous continuer ?', false)) {
                return self::SUCCESS;
            }
        }

        $user->assignRole($roleName);
        $this->info("✅ Rôle {$roleName} assigné à l'utilisateur {$user->name} ({$user->email}).");

        // Afficher les permissions de l'utilisateur
        $this->info('🔐 Permissions de l\'utilisateur :');
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        foreach ($userPermissions as $permission) {
            $this->line("  - {$permission}");
        }

        return self::SUCCESS;
    }

    /**
     * Liste toutes les permissions et rôles.
     */
    protected function listPermissions(): int
    {
        $this->info('📋 Liste des permissions et rôles...');

        // Permissions
        $this->info('🔐 Permissions disponibles :');
        $permissions = Permission::all();
        if ($permissions->isEmpty()) {
            $this->warn('  Aucune permission trouvée.');
        } else {
            foreach ($permissions as $permission) {
                $this->line("  - {$permission->name}");
            }
        }

        $this->newLine();

        // Rôles
        $this->info('👥 Rôles disponibles :');
        $roles = Role::all();
        if ($roles->isEmpty()) {
            $this->warn('  Aucun rôle trouvé.');
        } else {
            foreach ($roles as $role) {
                $permissionCount = $role->permissions->count();
                $userCount = $role->users->count();
                $this->line("  - {$role->name} ({$permissionCount} permissions, {$userCount} utilisateurs)");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Affiche l'aide de la commande.
     */
    protected function showHelp(): int
    {
        $this->error('❌ Action non reconnue : ' . $this->argument('action'));
        $this->line('Actions disponibles : setup, check, assign, list');
        return self::FAILURE;
    }

    /**
     * Affiche un résumé des permissions configurées.
     */
    protected function displayPermissionsSummary(): void
    {
        $this->newLine();
        $this->info('📊 Résumé de la configuration :');

        $permissionCount = Permission::count();
        $roleCount = Role::count();
        $userCount = User::role('task-manager')->count();

        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Permissions créées', $permissionCount],
                ['Rôles créés', $roleCount],
                ['Utilisateurs avec le rôle task-manager', $userCount],
            ]
        );

        $this->newLine();
        $this->info('🎯 Prochaines étapes :');
        $this->line('1. Assigner le rôle à un utilisateur :');
        $this->line('   php artisan task-manager:permissions assign --user=1');
        $this->line('   php artisan task-manager:permissions assign --user=test@example.com');
        $this->newLine();
        $this->line('2. Vérifier l\'état des permissions :');
        $this->line('   php artisan task-manager:permissions check');
    }
}
