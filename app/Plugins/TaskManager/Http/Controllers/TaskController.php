<?php

namespace App\Plugins\TaskManager\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Plugins\TaskManager\Models\Task;
use App\Plugins\TaskManager\Models\Category;
use App\Plugins\TaskManager\Http\Requests\TaskRequest;
use App\Plugins\TaskManager\Services\TaskService;
use App\Plugins\TaskManager\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Contrôleur pour la gestion des tâches
 *
 * Ce contrôleur gère toutes les opérations CRUD et les fonctionnalités
 * avancées pour les tâches du plugin Task Manager.
 */
class TaskController extends Controller
{
    /**
     * Le service de gestion des tâches.
     */
    protected TaskService $taskService;

    /**
     * Le service d'export.
     */
    protected ExportService $exportService;

    /**
     * Constructeur du contrôleur.
     */
    public function __construct(TaskService $taskService, ExportService $exportService)
    {
        $this->taskService = $taskService;
        $this->exportService = $exportService;

        // Middleware pour vérifier les permissions
        $this->middleware('auth');
        $this->middleware('task-manager.permission:view_tasks')->only(['index', 'show']);
        $this->middleware('task-manager.permission:create_tasks')->only(['create', 'store']);
        $this->middleware('task-manager.permission:edit_tasks')->only(['edit', 'update']);
        $this->middleware('task-manager.permission:delete_tasks')->only(['destroy']);
    }

    /**
     * Affiche la liste des tâches.
     */
    public function index(Request $request): View
    {
        // Récupération des filtres
        $filters = $request->only([
            'search', 'status', 'priority', 'assigned_to', 'category_id',
            'due_date_from', 'due_date_to', 'created_by', 'is_public'
        ]);

        // Récupération des tâches avec pagination
        $tasks = $this->taskService->getTasksWithFilters($filters, $request->get('per_page', 15));

        // Récupération des statistiques
        $stats = Cache::remember('task_stats_' . Auth::id(), 300, function () {
            return $this->taskService->getTaskStats();
        });

        // Récupération des catégories pour le filtre
        $categories = Category::active()->ordered()->get();

        // Récupération des utilisateurs pour le filtre
        $users = \App\Models\User::orderBy('name')->get();

        return view('task-manager::tasks.index', compact('tasks', 'stats', 'categories', 'users', 'filters'));
    }

    /**
     * Affiche le formulaire de création d'une tâche.
     */
    public function create(): View
    {
        // Récupération des catégories
        $categories = Category::active()->ordered()->get();

        // Récupération des utilisateurs
        $users = \App\Models\User::orderBy('name')->get();

        // Récupération des tâches pour les dépendances
        $availableTasks = Task::where('status', '!=', 'completed')
            ->where('id', '!=', request('parent_id'))
            ->get();

        return view('task-manager::tasks.create', compact('categories', 'users', 'availableTasks'));
    }

    /**
     * Enregistre une nouvelle tâche.
     */
    public function store(TaskRequest $request): RedirectResponse
    {
        try {
            $task = $this->taskService->createTask($request->validated());

            // Gestion des dépendances
            if ($request->has('dependencies')) {
                $this->taskService->addDependencies($task, $request->dependencies);
            }

            // Gestion des fichiers attachés
            if ($request->hasFile('attachments')) {
                $this->taskService->addAttachments($task, $request->file('attachments'));
            }

            return redirect()
                ->route('task-manager.tasks.show', $task)
                ->with('success', 'Tâche créée avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la tâche: ' . $e->getMessage());
        }
    }

    /**
     * Affiche une tâche spécifique.
     */
    public function show(Task $task): View
    {
        // Vérification des permissions
        if (!Gate::allows('view', $task)) {
            abort(403);
        }

        // Chargement des relations
        $task->load([
            'creator', 'assignee', 'category', 'comments.user', 'attachments',
            'tags', 'subtasks', 'dependencies', 'activities.user', 'timeEntries.user'
        ]);

        // Récupération des tâches liées
        $relatedTasks = $this->taskService->getRelatedTasks($task);

        // Récupération des statistiques de la tâche
        $taskStats = $this->taskService->getTaskStats($task);

        return view('task-manager::tasks.show', compact('task', 'relatedTasks', 'taskStats'));
    }

    /**
     * Affiche le formulaire de modification d'une tâche.
     */
    public function edit(Task $task): View
    {
        // Vérification des permissions
        if (!Gate::allows('update', $task)) {
            abort(403);
        }

        // Chargement des relations
        $task->load(['dependencies', 'tags']);

        // Récupération des catégories
        $categories = Category::active()->ordered()->get();

        // Récupération des utilisateurs
        $users = \App\Models\User::orderBy('name')->get();

        // Récupération des tâches pour les dépendances
        $availableTasks = Task::where('status', '!=', 'completed')
            ->where('id', '!=', $task->id)
            ->get();

        // Récupération des étiquettes
        $tags = \App\Plugins\TaskManager\Models\TaskTag::active()->get();

        return view('task-manager::tasks.edit', compact('task', 'categories', 'users', 'availableTasks', 'tags'));
    }

    /**
     * Met à jour une tâche.
     */
    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        // Vérification des permissions
        if (!Gate::allows('update', $task)) {
            abort(403);
        }

        try {
            $this->taskService->updateTask($task, $request->validated());

            // Gestion des dépendances
            if ($request->has('dependencies')) {
                $this->taskService->updateDependencies($task, $request->dependencies);
            }

            // Gestion des fichiers attachés
            if ($request->hasFile('attachments')) {
                $this->taskService->addAttachments($task, $request->file('attachments'));
            }

            return redirect()
                ->route('task-manager.tasks.show', $task)
                ->with('success', 'Tâche mise à jour avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de la tâche: ' . $e->getMessage());
        }
    }

    /**
     * Supprime une tâche.
     */
    public function destroy(Task $task): RedirectResponse
    {
        // Vérification des permissions
        if (!Gate::allows('delete', $task)) {
            abort(403);
        }

        try {
            $this->taskService->deleteTask($task);

            return redirect()
                ->route('task-manager.tasks.index')
                ->with('success', 'Tâche supprimée avec succès.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erreur lors de la suppression de la tâche: ' . $e->getMessage());
        }
    }

    /**
     * Change le statut d'une tâche.
     */
    public function changeStatus(Request $request, Task $task): JsonResponse
    {
        // Vérification des permissions
        if (!Gate::allows('update', $task)) {
            return response()->json(['error' => 'Permission refusée'], 403);
        }

        $status = $request->input('status');

        if (!$this->taskService->isValidStatus($status)) {
            return response()->json(['error' => 'Statut invalide'], 400);
        }

        try {
            $this->taskService->changeTaskStatus($task, $status);

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès',
                'status' => $status,
                'status_label' => $task->fresh()->status_label
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Change la priorité d'une tâche.
     */
    public function changePriority(Request $request, Task $task): JsonResponse
    {
        // Vérification des permissions
        if (!Gate::allows('update', $task)) {
            return response()->json(['error' => 'Permission refusée'], 403);
        }

        $priority = $request->input('priority');

        if (!$this->taskService->isValidPriority($priority)) {
            return response()->json(['error' => 'Priorité invalide'], 400);
        }

        try {
            $this->taskService->changeTaskPriority($task, $priority);

            return response()->json([
                'success' => true,
                'message' => 'Priorité mise à jour avec succès',
                'priority' => $priority,
                'priority_label' => $task->fresh()->priority_label
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Assigne une tâche à un utilisateur.
     */
    public function assign(Request $request, Task $task): JsonResponse
    {
        // Vérification des permissions
        if (!Gate::allows('assign', $task)) {
            return response()->json(['error' => 'Permission refusée'], 403);
        }

        $userId = $request->input('user_id');

        if (!$userId) {
            return response()->json(['error' => 'Utilisateur requis'], 400);
        }

        try {
            $this->taskService->assignTask($task, $userId);

            $assignee = \App\Models\User::find($userId);

            return response()->json([
                'success' => true,
                'message' => 'Tâche assignée avec succès',
                'assignee' => $assignee ? $assignee->name : null
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Met à jour le progrès d'une tâche.
     */
    public function updateProgress(Request $request, Task $task): JsonResponse
    {
        // Vérification des permissions
        if (!Gate::allows('update', $task)) {
            return response()->json(['error' => 'Permission refusée'], 403);
        }

        $progress = $request->input('progress');

        if (!is_numeric($progress) || $progress < 0 || $progress > 100) {
            return response()->json(['error' => 'Progrès invalide'], 400);
        }

        try {
            $this->taskService->updateTaskProgress($task, (int) $progress);

            return response()->json([
                'success' => true,
                'message' => 'Progrès mis à jour avec succès',
                'progress' => $progress
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Duplique une tâche.
     */
    public function duplicate(Task $task): RedirectResponse
    {
        // Vérification des permissions
        if (!Gate::allows('create', Task::class)) {
            abort(403);
        }

        try {
            $duplicatedTask = $this->taskService->duplicateTask($task);

            return redirect()
                ->route('task-manager.tasks.show', $duplicatedTask)
                ->with('success', 'Tâche dupliquée avec succès.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erreur lors de la duplication de la tâche: ' . $e->getMessage());
        }
    }

    /**
     * Exporte les tâches.
     */
    public function export(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        // Vérification des permissions
        if (!Gate::allows('export_tasks')) {
            abort(403);
        }

        $format = $request->input('format', 'xlsx');
        $filters = $request->only([
            'search', 'status', 'priority', 'assigned_to', 'category_id',
            'due_date_from', 'due_date_to', 'created_by', 'is_public'
        ]);

        try {
            return $this->exportService->exportTasks($format, $filters);
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Affiche le tableau de bord des tâches.
     */
    public function dashboard(): View
    {
        // Vérification des permissions
        if (!Gate::allows('view_tasks')) {
            abort(403);
        }

        // Récupération des statistiques
        $stats = Cache::remember('task_dashboard_stats_' . Auth::id(), 300, fn() => $this->taskService->getDashboardStats());

        // Récupération des tâches récentes
        $recentTasks = $this->taskService->getRecentTasks(10);

        // Récupération des tâches en retard
        $overdueTasks = $this->taskService->getOverdueTasks(5);

        // Récupération des tâches dues aujourd'hui
        $dueTodayTasks = $this->taskService->getDueTodayTasks(5);

        // Récupération des tâches par statut
        $tasksByStatus = $this->taskService->getTasksByStatus();

        // Récupération des tâches par priorité
        $tasksByPriority = $this->taskService->getTasksByPriority();

        return view('task-manager::dashboard', compact(
            'stats', 'recentTasks', 'overdueTasks', 'dueTodayTasks',
            'tasksByStatus', 'tasksByPriority'
        ));
    }

    /**
     * Affiche le calendrier des tâches.
     */
    public function calendar(): View
    {
        // Vérification des permissions
        if (!Gate::allows('view_tasks')) {
            abort(403);
        }

        // Récupération des tâches pour le calendrier
        $tasks = $this->taskService->getTasksForCalendar();

        return view('task-manager::calendar', compact('tasks'));
    }

    /**
     * Affiche les rapports de tâches.
     */
    public function reports(): View
    {
        // Vérification des permissions
        if (!Gate::allows('view_reports')) {
            abort(403);
        }

        // Récupération des données pour les rapports
        $reportData = $this->taskService->getReportData();

        return view('task-manager::reports', compact('reportData'));
    }

    /**
     * API pour récupérer les tâches (AJAX).
     */
    public function apiIndex(Request $request): JsonResponse
    {
        // Vérification des permissions
        if (!Gate::allows('view_tasks')) {
            return response()->json(['error' => 'Permission refusée'], 403);
        }

        $filters = $request->only([
            'search', 'status', 'priority', 'assigned_to', 'category_id',
            'due_date_from', 'due_date_to', 'created_by', 'is_public'
        ]);

        $tasks = $this->taskService->getTasksWithFilters($filters, $request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'pagination' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ]
        ]);
    }
}
