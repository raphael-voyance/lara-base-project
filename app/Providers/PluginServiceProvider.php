<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

/**
 * Service Provider Principal pour la Gestion des Plugins
 *
 * Ce service provider découvre et enregistre automatiquement
 * tous les plugins activés dans l'application.
 */
class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Chargement de la configuration des plugins
        $this->mergeConfigFrom(__DIR__.'/../../config/plugins.php', 'plugins');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadPlugins();
    }

    /**
     * Charge tous les plugins activés.
     */
    protected function loadPlugins(): void
    {
        $pluginsConfig = config('plugins', []);
        $enabledPlugins = $pluginsConfig['enabled'] ?? [];

        foreach ($enabledPlugins as $pluginName => $isEnabled) {
            if ($isEnabled) {
                $this->loadPlugin($pluginName);
            }
        }
    }

    /**
     * Charge un plugin spécifique.
     */
    protected function loadPlugin(string $pluginName): void
    {
        $pluginsPath = config('plugins.plugins_path', app_path('Plugins'));
        $pluginPath = $pluginsPath . '/' . $pluginName;
        $pluginNamespace = config('plugins.namespace', 'App\\Plugins');

        // Vérifier si le plugin existe
        if (!is_dir($pluginPath)) {
            return;
        }

        // Charger le service provider du plugin
        $serviceProviderClass = $pluginNamespace . '\\' . $pluginName . '\\Providers\\PluginServiceProvider';

        if (class_exists($serviceProviderClass)) {
            $this->app->register($serviceProviderClass);
        }

        // Charger les routes du plugin
        $routesPath = $pluginPath . '/routes/web.php';
        if (file_exists($routesPath)) {
            Route::middleware('web')->group($routesPath);
        }

        // Charger les vues du plugin
        $viewsPath = $pluginPath . '/resources/views';
        if (is_dir($viewsPath)) {
            View::addNamespace(strtolower($pluginName), $viewsPath);
        }

        // Publier les assets du plugin
        $assetsPath = $pluginPath . '/resources/assets';
        if (is_dir($assetsPath)) {
            $this->publishes([
                $assetsPath => public_path('vendor/' . strtolower($pluginName)),
            ], strtolower($pluginName) . '-assets');
        }
    }
}
