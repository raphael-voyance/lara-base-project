<?php

use Illuminate\Support\Facades\Route;
use App\Plugins\TaskManager\Http\Controllers\TaskController;
use App\Plugins\TaskManager\Http\Livewire\TaskList;

/**
 * Routes du Plugin Task Manager
 *
 * Ce fichier définit toutes les routes nécessaires pour le plugin
 * de gestion de tâches.
 */

// Groupe de routes avec préfixe et middleware
Route::group([
    'prefix' => config('task-manager.route_prefix', 'tasks'),
    'middleware' => config('task-manager.middleware', ['web', 'auth']),
    'as' => 'task-manager.'
], function () {

    // Route pour le tableau de bord
    Route::get('/', [TaskController::class, 'dashboard'])->name('dashboard');

    // Route pour le calendrier
    Route::get('/calendar', [TaskController::class, 'calendar'])->name('calendar');

    // Route pour les rapports
    Route::get('/reports', [TaskController::class, 'reports'])->name('reports');

    // Routes pour les tâches
    Route::group(['prefix' => 'tasks', 'as' => 'tasks.'], function () {
        // Liste des tâches
        Route::get('/', [TaskController::class, 'index'])->name('index');

        // Création d'une tâche
        Route::get('/create', [TaskController::class, 'create'])->name('create');
        Route::post('/', [TaskController::class, 'store'])->name('store');

        // Affichage d'une tâche
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');

        // Modification d'une tâche
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');

        // Suppression d'une tâche
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');

        // Actions AJAX pour les tâches
        Route::group(['prefix' => '{task}', 'as' => 'actions.'], function () {
            // Changement de statut
            Route::patch('/status', [TaskController::class, 'changeStatus'])->name('change-status');

            // Changement de priorité
            Route::patch('/priority', [TaskController::class, 'changePriority'])->name('change-priority');

            // Assignation à un utilisateur
            Route::patch('/assign', [TaskController::class, 'assign'])->name('assign');

            // Mise à jour du progrès
            Route::patch('/progress', [TaskController::class, 'updateProgress'])->name('update-progress');

            // Duplication d'une tâche
            Route::post('/duplicate', [TaskController::class, 'duplicate'])->name('duplicate');
        });

        // Export des tâches
        Route::get('/export/{format?}', [TaskController::class, 'export'])->name('export');

        // API pour les tâches (AJAX)
        Route::get('/api/list', [TaskController::class, 'apiIndex'])->name('api.index');
    });

    // Routes pour les catégories (si nécessaire)
    Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
        Route::get('/', function () {
            return view('task-manager::categories.index');
        })->name('index');

        Route::get('/create', function () {
            return view('task-manager::categories.create');
        })->name('create');

        Route::post('/', function () {
            // Logique de création de catégorie
        })->name('store');

        Route::get('/{category}', function ($category) {
            return view('task-manager::categories.show', compact('category'));
        })->name('show');

        Route::get('/{category}/edit', function ($category) {
            return view('task-manager::categories.edit', compact('category'));
        })->name('edit');

        Route::put('/{category}', function ($category) {
            // Logique de mise à jour de catégorie
        })->name('update');

        Route::delete('/{category}', function ($category) {
            // Logique de suppression de catégorie
        })->name('destroy');
    });

    // Routes pour les étiquettes (si nécessaire)
    Route::group(['prefix' => 'tags', 'as' => 'tags.'], function () {
        Route::get('/', function () {
            return view('task-manager::tags.index');
        })->name('index');

        Route::post('/', function () {
            // Logique de création d'étiquette
        })->name('store');

        Route::delete('/{tag}', function ($tag) {
            // Logique de suppression d'étiquette
        })->name('destroy');
    });

    // Routes pour les rapports
    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
        Route::get('/tasks-by-status', function () {
            return view('task-manager::reports.tasks-by-status');
        })->name('tasks-by-status');

        Route::get('/tasks-by-priority', function () {
            return view('task-manager::reports.tasks-by-priority');
        })->name('tasks-by-priority');

        Route::get('/overdue-tasks', function () {
            return view('task-manager::reports.overdue-tasks');
        })->name('overdue-tasks');

        Route::get('/user-performance', function () {
            return view('task-manager::reports.user-performance');
        })->name('user-performance');

        Route::get('/time-tracking', function () {
            return view('task-manager::reports.time-tracking');
        })->name('time-tracking');
    });

    // Routes pour les paramètres
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('/', function () {
            return view('task-manager::settings.index');
        })->name('index');

        Route::get('/permissions', function () {
            return view('task-manager::settings.permissions');
        })->name('permissions');

        Route::get('/notifications', function () {
            return view('task-manager::settings.notifications');
        })->name('notifications');

        Route::get('/integrations', function () {
            return view('task-manager::settings.integrations');
        })->name('integrations');
    });

    // Routes pour les webhooks (si nécessaire)
    Route::group(['prefix' => 'webhooks', 'as' => 'webhooks.'], function () {
        Route::get('/', function () {
            return view('task-manager::webhooks.index');
        })->name('index');

        Route::post('/', function () {
            // Logique de création de webhook
        })->name('store');

        Route::delete('/{webhook}', function ($webhook) {
            // Logique de suppression de webhook
        })->name('destroy');
    });
});

// Routes pour les composants Livewire
Route::group([
    'prefix' => config('task-manager.route_prefix', 'tasks'),
    'middleware' => config('task-manager.middleware', ['web', 'auth']),
], function () {
    // Route pour le composant Livewire de liste des tâches
    Route::get('/livewire/task-list', TaskList::class)->name('task-manager.livewire.task-list');
});

// Routes pour les assets et ressources statiques
Route::group([
    'prefix' => 'task-manager/assets',
    'middleware' => ['web'],
], function () {
    Route::get('/css/{file}', function ($file) {
        $path = public_path("vendor/task-manager/css/{$file}");
        if (file_exists($path)) {
            return response()->file($path, ['Content-Type' => 'text/css']);
        }
        abort(404);
    })->where('file', '.*\.css$');

    Route::get('/js/{file}', function ($file) {
        $path = public_path("vendor/task-manager/js/{$file}");
        if (file_exists($path)) {
            return response()->file($path, ['Content-Type' => 'application/javascript']);
        }
        abort(404);
    })->where('file', '.*\.js$');

    Route::get('/images/{file}', function ($file) {
        $path = public_path("vendor/task-manager/images/{$file}");
        if (file_exists($path)) {
            return response()->file($path);
        }
        abort(404);
    })->where('file', '.*\.(png|jpg|jpeg|gif|svg)$');
});
