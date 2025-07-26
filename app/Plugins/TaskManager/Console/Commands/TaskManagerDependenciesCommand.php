<?php

namespace App\Plugins\TaskManager\Console\Commands;

use Illuminate\Console\Command;

/**
 * Commande de gestion des dépendances du plugin Task Manager
 *
 * Cette commande permet d'installer, vérifier et gérer
 * toutes les dépendances Composer requises par le plugin.
 */
class TaskManagerDependenciesCommand extends Command
{
    /**
     * Le nom et la signature de la commande.
     */
    protected $signature = 'task-manager:dependencies
                            {action=check : Action à effectuer (check, install, update, list)}
                            {--package= : Package spécifique à traiter}
                            {--force : Forcer l\'installation sans confirmation}
                            {--dev : Inclure les dépendances de développement}';

    /**
     * La description de la commande.
     */
    protected $description = 'Gère les dépendances Composer du plugin Task Manager';

    /**
     * Exécute la commande.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'check':
                return $this->checkDependencies();
            case 'install':
                return $this->installDependencies();
            case 'update':
                return $this->updateDependencies();
            case 'list':
                return $this->listDependencies();
            default:
                $this->error("Action inconnue: {$action}");
                $this->info('Actions disponibles: check, install, update, list');
                return self::FAILURE;
        }
    }

    /**
     * Vérifie l'état des dépendances.
     */
    protected function checkDependencies(): int
    {
        $this->info('🔍 Vérification des dépendances...');
        $this->newLine();

        $dependencies = $this->getDependencies();
        $missingPackages = [];
        $installedPackages = [];

        foreach ($dependencies as $package => $config) {
            $version = $config['version'] ?? '*';
            $description = $config['description'] ?? '';
            $required = $config['required'] ?? false;

            $this->info("📦 {$package} ({$version})");
            if ($description) {
                $this->line("   {$description}");
            }

            if ($this->isPackageInstalled($package)) {
                $this->info("   ✅ Installé");
                $installedPackages[] = $package;
            } else {
                $status = $required ? "❌ Manquant (requis)" : "⚠️  Manquant (optionnel)";
                $this->warn("   {$status}");
                $missingPackages[] = $package;
            }

            $this->newLine();
        }

        // Résumé
        $this->info('📊 Résumé:');
        $this->info("   ✅ Installés: " . count($installedPackages));
        $this->info("   ❌ Manquants: " . count($missingPackages));

        if (!empty($missingPackages)) {
            $this->warn('⚠️  Packages manquants: ' . implode(', ', $missingPackages));
            $this->info('💡 Utilisez: php artisan task-manager:dependencies install');
        }

        return self::SUCCESS;
    }

    /**
     * Installe les dépendances manquantes.
     */
    protected function installDependencies(): int
    {
        $this->info('📦 Installation des dépendances...');
        $this->newLine();

        $dependencies = $this->getDependencies();
        $packageFilter = $this->option('package');
        $force = $this->option('force');

        if ($packageFilter) {
            if (!isset($dependencies[$packageFilter])) {
                $this->error("Package non trouvé: {$packageFilter}");
                return self::FAILURE;
            }
            $dependencies = [$packageFilter => $dependencies[$packageFilter]];
        }

        $installedCount = 0;
        $failedCount = 0;

        foreach ($dependencies as $package => $config) {
            $version = $config['version'] ?? '*';
            $description = $config['description'] ?? '';
            $required = $config['required'] ?? false;
            $isDev = $config['dev'] ?? false;

            $this->info("📦 {$package} ({$version})");
            if ($description) {
                $this->line("   {$description}");
            }

            // Vérifier si déjà installé
            if ($this->isPackageInstalled($package)) {
                $this->info("   ✅ Déjà installé");
                continue;
            }

            // Demander confirmation pour les packages optionnels
            if (!$required && !$force) {
                if (!$this->confirm("   Installer ce package optionnel ?", false)) {
                    $this->line("   ⏭️  Ignoré");
                    continue;
                }
            }

            try {
                $this->installPackage($package, $version, $isDev);
                $this->runPostInstallScripts($package);
                $installedCount++;
            } catch (\Exception $e) {
                $this->error("   ❌ Échec: " . $e->getMessage());
                $failedCount++;

                if ($required && !$force) {
                    if (!$this->confirm("   Ce package est requis. Continuer ?", false)) {
                        return self::FAILURE;
                    }
                }
            }

            $this->newLine();
        }

        // Résumé
        $this->info('📊 Résumé de l\'installation:');
        $this->info("   ✅ Installés: {$installedCount}");
        $this->info("   ❌ Échoués: {$failedCount}");

        if ($installedCount > 0) {
            $this->info('🔄 Mise à jour de l\'autoloader...');
            $this->executeShellCommand('composer dump-autoload');
        }

        return $failedCount === 0 ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Met à jour les dépendances existantes.
     */
    protected function updateDependencies(): int
    {
        $this->info('🔄 Mise à jour des dépendances...');
        $this->newLine();

        $dependencies = $this->getDependencies();
        $packageFilter = $this->option('package');

        if ($packageFilter) {
            if (!isset($dependencies[$packageFilter])) {
                $this->error("Package non trouvé: {$packageFilter}");
                return self::FAILURE;
            }
            $dependencies = [$packageFilter => $dependencies[$packageFilter]];
        }

        $updatedCount = 0;
        $failedCount = 0;

        foreach ($dependencies as $package => $config) {
            if (!$this->isPackageInstalled($package)) {
                $this->warn("⚠️  {$package} n'est pas installé");
                continue;
            }

            $this->info("🔄 Mise à jour de {$package}...");

            try {
                $this->executeShellCommand("composer update {$package} --no-interaction");
                $this->runPostInstallScripts($package);
                $updatedCount++;
                $this->info("   ✅ Mis à jour");
            } catch (\Exception $e) {
                $this->error("   ❌ Échec: " . $e->getMessage());
                $failedCount++;
            }

            $this->newLine();
        }

        $this->info('📊 Résumé de la mise à jour:');
        $this->info("   ✅ Mis à jour: {$updatedCount}");
        $this->info("   ❌ Échoués: {$failedCount}");

        return $failedCount === 0 ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Liste toutes les dépendances configurées.
     */
    protected function listDependencies(): int
    {
        $this->info('📋 Liste des dépendances configurées:');
        $this->newLine();

        $dependencies = $this->getDependencies();

        $headers = ['Package', 'Version', 'Description', 'Requis', 'Installé'];
        $rows = [];

        foreach ($dependencies as $package => $config) {
            $version = $config['version'] ?? '*';
            $description = $config['description'] ?? '';
            $required = $config['required'] ?? false;
            $installed = $this->isPackageInstalled($package);

            $rows[] = [
                $package,
                $version,
                $description,
                $required ? 'Oui' : 'Non',
                $installed ? '✅' : '❌',
            ];
        }

        $this->table($headers, $rows);

        return self::SUCCESS;
    }

    /**
     * Récupère la configuration des dépendances.
     */
    protected function getDependencies(): array
    {
        return config('task-manager.dependencies.required', []);
    }

    /**
     * Vérifie si un package est installé.
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
     * Exécute les scripts post-installation.
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
}
