<?php

namespace App\Plugins\TaskManager\Providers;

use Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Plugins\TaskManager\Models\Task;
use App\Plugins\TaskManager\Models\Category;
use App\Plugins\TaskManager\Policies\TaskPolicy;
use App\Plugins\TaskManager\Policies\CategoryPolicy;

/**
 * Service Provider du Plugin Task Manager
 *
 * Ce service provider enregistre toutes les fonctionnalités spécifiques
 * au plugin de gestion de tâches.
 */
class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Enregistrement du fichier de configuration principal
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'task-manager'
        );

        // Enregistrement du fichier de configuration des dépendances
        $this->mergeConfigFrom(
            __DIR__.'/../config/dependencies.php', 'task-manager.dependencies'
        );

        // Enregistrement des facades personnalisées
        $this->app->singleton('task-manager', function ($app) {
            return new \App\Plugins\TaskManager\Services\TaskManagerService(
                $app->make(\App\Plugins\TaskManager\Services\TaskService::class),
                $app->make(\App\Plugins\TaskManager\Services\CategoryService::class)
            );
        });

        // Enregistrement des repositories
        $this->app->bind(\App\Plugins\TaskManager\Repositories\TaskRepository::class);
        $this->app->bind(\App\Plugins\TaskManager\Repositories\CategoryRepository::class);

        // Enregistrement des services
        $this->app->bind(\App\Plugins\TaskManager\Services\TaskService::class);
        $this->app->bind(\App\Plugins\TaskManager\Services\CategoryService::class);
        $this->app->bind(\App\Plugins\TaskManager\Services\NotificationService::class);
        $this->app->bind(\App\Plugins\TaskManager\Services\ExportService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publication des fichiers de configuration
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('task-manager.php'),
        ], 'task-manager-config');

        // Publication des migrations
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'task-manager-migrations');

        // Publication des assets
        $this->publishes([
            __DIR__.'/../resources/assets/' => public_path('vendor/task-manager'),
        ], 'task-manager-assets');

        // Publication des vues
        $this->publishes([
            __DIR__.'/../resources/views/' => resource_path('views/vendor/task-manager'),
        ], 'task-manager-views');

        // Chargement des routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Chargement des vues
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'task-manager');

        // Chargement des traductions
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'task-manager');

        // Enregistrement des policies
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);

        // Enregistrement des composants Blade
        $this->registerBladeComponents();

        // Enregistrement des directives Blade personnalisées
        $this->registerBladeDirectives();

        // Enregistrement des macros (désactivé temporairement)
        // $this->registerMacros();

        // Enregistrement des listeners d'événements
        $this->registerEventListeners();

        // Enregistrement des commandes Artisan
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Plugins\TaskManager\Console\Commands\TaskManagerInstallCommand::class,
                \App\Plugins\TaskManager\Console\Commands\TaskManagerSeedCommand::class,
                \App\Plugins\TaskManager\Console\Commands\TaskManagerExportCommand::class,
                \App\Plugins\TaskManager\Console\Commands\TaskManagerDependenciesCommand::class,
                \App\Plugins\TaskManager\Console\Commands\TaskManagerPermissionsCommand::class,
            ]);
        }

        // Enregistrement des middlewares
        $this->registerMiddlewares();

        // Enregistrement des validations
        $this->registerValidations();

        // Enregistrement des observers
        $this->registerObservers();
    }

    /**
     * Enregistre les composants Blade personnalisés
     */
    protected function registerBladeComponents(): void
    {
        Blade::component('task-manager::components.task-card', 'task-card');
        Blade::component('task-manager::components.task-form', 'task-form');
        Blade::component('task-manager::components.task-list', 'task-list');
        Blade::component('task-manager::components.category-select', 'category-select');
        Blade::component('task-manager::components.priority-badge', 'priority-badge');
        Blade::component('task-manager::components.status-badge', 'status-badge');
        Blade::component('task-manager::components.task-progress', 'task-progress');
        Blade::component('task-manager::components.task-calendar', 'task-calendar');
        Blade::component('task-manager::components.task-dashboard', 'task-dashboard');
    }

    /**
     * Enregistre les directives Blade personnalisées
     */
    protected function registerBladeDirectives(): void
    {
        // Directive pour vérifier les permissions
        Blade::directive('taskPermission', function ($expression) {
            return "<?php if (Gate::allows($expression)): ?>";
        });

        Blade::directive('endTaskPermission', function () {
            return "<?php endif; ?>";
        });

        // Directive pour afficher le statut d'une tâche
        Blade::directive('taskStatus', function ($expression) {
            return "<?php echo app('task-manager')->getStatusLabel($expression); ?>";
        });

        // Directive pour afficher la priorité d'une tâche
        Blade::directive('taskPriority', function ($expression) {
            return "<?php echo app('task-manager')->getPriorityLabel($expression); ?>";
        });

        // Directive pour afficher la date d'échéance
        Blade::directive('taskDueDate', function ($expression) {
            return "<?php echo app('task-manager')->formatDueDate($expression); ?>";
        });

        // Directive pour vérifier si une tâche est en retard
        Blade::directive('taskOverdue', function ($expression) {
            return "<?php if (app('task-manager')->isOverdue($expression)): ?>";
        });

        Blade::directive('endTaskOverdue', function () {
            return "<?php endif; ?>";
        });
    }



    /**
     * Enregistre les listeners d'événements
     */
    protected function registerEventListeners(): void
    {
        // Listeners temporairement désactivés pour éviter les erreurs de classes manquantes
        // Event::listen(\App\Plugins\TaskManager\Events\TaskCreated::class,
        //     \App\Plugins\TaskManager\Listeners\SendTaskCreatedNotification::class);
        // Event::listen(\App\Plugins\TaskManager\Events\TaskAssigned::class,
        //     \App\Plugins\TaskManager\Listeners\SendTaskAssignedNotification::class);
        // Event::listen(\App\Plugins\TaskManager\Events\TaskCompleted::class,
        //     \App\Plugins\TaskManager\Listeners\SendTaskCompletedNotification::class);
        // Event::listen(\App\Plugins\TaskManager\Events\TaskUpdated::class,
        //     \App\Plugins\TaskManager\Listeners\SendTaskUpdatedNotification::class);
        // Event::listen(\App\Plugins\TaskManager\Events\TaskDeleted::class,
        //     \App\Plugins\TaskManager\Listeners\SendTaskDeletedNotification::class);
    }

    /**
     * Enregistre les middlewares
     */
    protected function registerMiddlewares(): void
    {
        // Middlewares temporairement désactivés pour éviter les erreurs de classes manquantes
        // Route::aliasMiddleware('task-manager.auth', \App\Plugins\TaskManager\Middleware\TaskManagerAuth::class);
        // Route::aliasMiddleware('task-manager.permission', \App\Plugins\TaskManager\Middleware\TaskManagerPermission::class);
        // Route::aliasMiddleware('task-manager.rate-limit', \App\Plugins\TaskManager\Middleware\TaskManagerRateLimit::class);
    }

    /**
     * Enregistre les validations
     */
    protected function registerValidations(): void
    {
        // Validations temporairement désactivées pour éviter les erreurs
        // Validator::extend('future_date', function ($attribute, $value, $parameters, $validator) {
        //     return \Carbon\Carbon::parse($value)->isFuture();
        // }, 'La date d\'échéance doit être dans le futur.');

        // Validator::extend('valid_priority', function ($attribute, $value, $parameters, $validator) {
        //     return in_array($value, ['low', 'medium', 'high', 'urgent']);
        // }, 'La priorité sélectionnée n\'est pas valide.');

        // Validator::extend('valid_status', function ($attribute, $value, $parameters, $validator) {
        //     return in_array($value, ['pending', 'in_progress', 'completed', 'cancelled']);
        // }, 'Le statut sélectionné n\'est pas valide.');
    }

    /**
     * Enregistre les observers
     */
    protected function registerObservers(): void
    {
        // Observers temporairement désactivés pour éviter les erreurs de classes manquantes
        // Task::observe(\App\Plugins\TaskManager\Observers\TaskObserver::class);
        // Category::observe(\App\Plugins\TaskManager\Observers\CategoryObserver::class);
    }
}
