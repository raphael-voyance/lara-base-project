<?php

namespace App\Plugins\TaskManager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Plugins\TaskManager\Models\Category;

/**
 * Commande d'installation du plugin Task Manager
 *
 * Cette commande automatise l'installation complète du plugin :
 * - Exécution des migrations
 * - Création des données de base
 * - Configuration des permissions
 * - Publication des assets
 */
class TaskManagerInstallCommand extends Command
{
    /**
     * Le nom et la signature de la commande.
     */
    protected $signature = 'task-manager:install
                            {--force : Forcer l\'installation même si déjà installé}
                            {--seed : Créer les données de base}
                            {--publish : Publier les assets et configurations}
                            {--dependencies : Installer les dépendances Composer}';

    /**
     * La description de la commande.
     */
    protected $description = 'Installe le plugin Task Manager avec toutes ses dépendances';

    /**
     * Exécute la commande.
     */
    public function handle(): int
    {
        $this->info('🚀 Installation du plugin Task Manager...');

        // Vérifier si déjà installé
        if ($this->isAlreadyInstalled() && !$this->option('force')) {
            $this->warn('⚠️  Le plugin Task Manager semble déjà être installé.');

            if (!$this->confirm('Voulez-vous continuer l\'installation ?', false)) {
                $this->info('❌ Installation annulée.');
                return self::FAILURE;
            }
        }

        try {
            // 1. Installer les dépendances Composer
            if ($this->option('dependencies')) {
                $this->installDependencies();
            }

            // 2. Publier les configurations et assets
            if ($this->option('publish')) {
                $this->publishAssets();
            }

            // 3. Exécuter les migrations
            $this->runMigrations();

            // 4. Créer les données de base
            if ($this->option('seed')) {
                $this->seedData();
            }

            // 5. Configurer les permissions
            $this->setupPermissions();

            // 6. Marquer comme installé
            $this->markAsInstalled();

            $this->info('✅ Plugin Task Manager installé avec succès !');
            $this->newLine();

            $this->displayNextSteps();

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'installation : ' . $e->getMessage());
            $this->error('Stack trace : ' . $e->getTraceAsString());
            return self::FAILURE;
        }
    }

    /**
     * Vérifie si le plugin est déjà installé.
     */
    protected function isAlreadyInstalled(): bool
    {
        return Schema::hasTable('tasks') &&
               Schema::hasTable('task_categories') &&
               DB::table('tasks')->count() > 0;
    }

        /**
     * Installe les dépendances Composer requises.
     */
    protected function installDependencies(): void
    {
        $this->info('📦 Installation des dépendances Composer...');

        // Charger la configuration des dépendances
        $dependenciesConfig = config('task-manager.dependencies', []);
        $dependencies = $dependenciesConfig['required'] ?? [];

        if (empty($dependencies)) {
            $this->warn('⚠️  Aucune dépendance configurée pour ce plugin.');
            return;
        }

        $this->info('🔍 Vérification des dépendances...');
        $this->newLine();

        $installedPackages = [];
        $failedPackages = [];

        foreach ($dependencies as $package => $config) {
            $version = $config['version'] ?? '*';
            $description = $config['description'] ?? '';
            $required = $config['required'] ?? false;
            $isDev = $config['dev'] ?? false;

            $this->info("📦 {$package} ({$version})");
            if ($description) {
                $this->line("   {$description}");
            }

            // Vérifier si le package est déjà installé
            if ($this->isPackageInstalled($package)) {
                $this->info("   ✅ Déjà installé");
                $installedPackages[] = $package;
                continue;
            }

            // Demander confirmation pour les packages optionnels
            if (!$required && !$this->option('force')) {
                if (!$this->confirm("   Installer ce package optionnel ?", false)) {
                    $this->line("   ⏭️  Ignoré");
                    continue;
                }
            }

            try {
                $this->installPackage($package, $version, $isDev);
                $installedPackages[] = $package;

                // Exécuter les scripts post-installation
                $this->runPostInstallScripts($package);

            } catch (\Exception $e) {
                $this->error("   ❌ Échec: " . $e->getMessage());
                $failedPackages[] = $package;

                if ($required) {
                    if (!$this->confirm("   Ce package est requis. Voulez-vous continuer quand même ?", false)) {
                        throw new \Exception("Installation échouée pour le package requis: {$package}");
                    }
                }
            }

            $this->newLine();
        }

        // Résumé de l'installation
        $this->info('📊 Résumé de l\'installation:');
        $this->info("   ✅ Installés: " . count($installedPackages));
        $this->info("   ❌ Échoués: " . count($failedPackages));

        if (!empty($failedPackages)) {
            $this->warn('⚠️  Packages échoués: ' . implode(', ', $failedPackages));
        }

        if (!empty($installedPackages)) {
            $this->info('🔄 Mise à jour de l\'autoloader...');
            $this->executeShellCommand('composer dump-autoload');
        }
    }

    /**
     * Vérifie si un package est déjà installé.
     */
    protected function isPackageInstalled(string $package): bool
    {
        $composerLock = base_path('composer.lock');

        if (!file_exists($composerLock)) {
            return false;
        }

        $lockData = json_decode(file_get_contents($composerLock), true);
        $packages = $lockData['packages'] ?? [];

        foreach ($packages as $installedPackage) {
            if ($installedPackage['name'] === $package) {
                return true;
            }
        }

        return false;
    }

    /**
     * Installe un package spécifique.
     */
    protected function installPackage(string $package, string $version, bool $isDev = false): void
    {
        $devFlag = $isDev ? '--dev' : '';
        $command = "composer require {$package}:{$version} {$devFlag} --no-interaction --no-progress";

        $this->line("   🔄 Installation en cours...");

        $output = shell_exec($command . ' 2>&1');

        if (strpos($output, 'error') !== false || strpos($output, 'failed') !== false) {
            throw new \Exception("Erreur Composer: " . trim($output));
        }

        $this->info("   ✅ Installé avec succès");
    }

    /**
     * Exécute les scripts post-installation pour un package.
     */
    protected function runPostInstallScripts(string $package): void
    {
        $dependenciesConfig = config('task-manager.dependencies', []);
        $postInstallScripts = $dependenciesConfig['post_install_scripts'] ?? [];

        if (!isset($postInstallScripts[$package])) {
            return;
        }

        $scripts = $postInstallScripts[$package];

        $this->line("   🔧 Exécution des scripts post-installation...");

        foreach ($scripts as $script) {
            try {
                $this->executeShellCommand($script);
                $this->line("     ✅ " . $script);
            } catch (\Exception $e) {
                $this->warn("     ⚠️  Échec: " . $script);
            }
        }
    }

        /**
     * Exécute une commande shell.
     */
    protected function executeShellCommand(string $command): void
    {
        $output = shell_exec($command . ' 2>&1');

        if (strpos($output, 'error') !== false || strpos($output, 'failed') !== false) {
            throw new \Exception("Commande échouée: {$command}");
        }
    }

    /**
     * Publie les assets et configurations.
     */
    protected function publishAssets(): void
    {
        $this->info('📦 Publication des assets et configurations...');

        // Publier la configuration
        $this->call('vendor:publish', [
            '--tag' => 'task-manager-config',
            '--force' => true
        ]);

        // Publier les migrations
        $this->call('vendor:publish', [
            '--tag' => 'task-manager-migrations',
            '--force' => true
        ]);

        // Publier les assets
        $this->call('vendor:publish', [
            '--tag' => 'task-manager-assets',
            '--force' => true
        ]);

        // Publier les vues (optionnel)
        if ($this->confirm('Publier les vues du plugin ?', false)) {
            $this->call('vendor:publish', [
                '--tag' => 'task-manager-views',
                '--force' => true
            ]);
        }

        $this->info('✅ Assets publiés avec succès.');
    }

    /**
     * Exécute les migrations.
     */
    protected function runMigrations(): void
    {
        $this->info('🗄️  Exécution des migrations...');

        // Vérifier si les migrations existent
        $migrationPath = database_path('migrations/*_create_task_manager_tables.php');
        if (empty(glob($migrationPath))) {
            $this->warn('⚠️  Aucune migration trouvée. Publication des migrations...');
            $this->call('vendor:publish', [
                '--tag' => 'task-manager-migrations',
                '--force' => true
            ]);
        }

        // Exécuter les migrations
        $this->call('migrate', [
            '--force' => true
        ]);

        $this->info('✅ Migrations exécutées avec succès.');
    }

    /**
     * Crée les données de base.
     */
    protected function seedData(): void
    {
        $this->info('🌱 Création des données de base...');

        // Créer les catégories par défaut
        $defaultCategories = [
            ['name' => 'Général', 'description' => 'Tâches générales', 'color' => '#3B82F6'],
            ['name' => 'Urgent', 'description' => 'Tâches urgentes', 'color' => '#EF4444'],
            ['name' => 'Planifié', 'description' => 'Tâches planifiées', 'color' => '#10B981'],
            ['name' => 'Maintenance', 'description' => 'Tâches de maintenance', 'color' => '#F59E0B'],
        ];

        foreach ($defaultCategories as $categoryData) {
            Category::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }

        $this->info('✅ Données de base créées avec succès.');
    }

    /**
     * Configure les permissions.
     */
    protected function setupPermissions(): void
    {
        $this->info('🔐 Configuration des permissions...');

        // Vérifier si spatie/laravel-permission est installé
        if (!class_exists('Spatie\Permission\PermissionServiceProvider')) {
            $this->warn('⚠️  Package spatie/laravel-permission non installé. Installation des permissions ignorée.');
            return;
        }

        try {
            // Créer les permissions de base
            $permissions = [
                'view_tasks',
                'create_tasks',
                'edit_tasks',
                'delete_tasks',
                'assign_tasks',
                'export_tasks',
                'manage_categories',
                'view_reports',
            ];

            foreach ($permissions as $permission) {
                \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            }

            // Créer le rôle "Task Manager"
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'task-manager']);
            $role->givePermissionTo($permissions);

            $this->info('✅ Permissions configurées avec succès.');
        } catch (\Exception $e) {
            $this->warn('⚠️  Erreur lors de la configuration des permissions : ' . $e->getMessage());
        }
    }

    /**
     * Marque le plugin comme installé.
     */
    protected function markAsInstalled(): void
    {
        // Créer un fichier de marqueur ou enregistrer en base
        $installFile = storage_path('app/task-manager-installed.json');

        file_put_contents($installFile, json_encode([
            'installed_at' => now()->toISOString(),
            'version' => '1.0.0',
            'laravel_version' => app()->version(),
        ]));

        $this->info('✅ Plugin marqué comme installé.');
    }

    /**
     * Affiche les prochaines étapes.
     */
    protected function displayNextSteps(): void
    {
        $this->info('📋 Prochaines étapes :');
        $this->newLine();

        $this->line('1. Configurez les variables d\'environnement dans votre fichier .env :');
        $this->line('   TASK_MANAGER_NAME="Gestionnaire de Tâches"');
        $this->line('   TASK_MANAGER_ROUTE_PREFIX="tasks"');
        $this->newLine();

        $this->line('2. Compilez les assets :');
        $this->line('   npm run build');
        $this->newLine();

        $this->line('3. Accédez à l\'interface :');
        $this->line('   ' . url('/tasks'));
        $this->newLine();

        $this->line('4. Pour créer des données de test :');
        $this->line('   php artisan task-manager:seed');
        $this->newLine();

        $this->info('🎉 Le plugin Task Manager est maintenant prêt à être utilisé !');
    }
}
