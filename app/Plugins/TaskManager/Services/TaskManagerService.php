<?php

namespace App\Plugins\TaskManager\Services;

use App\Plugins\TaskManager\Models\Task;
use App\Plugins\TaskManager\Models\Category;
use Illuminate\Support\Collection;

/**
 * Service principal du plugin Task Manager
 *
 * Ce service sert de facade pour accéder aux fonctionnalités
 * principales du plugin de gestion de tâches.
 */
class TaskManagerService
{
    protected TaskService $taskService;
    protected CategoryService $categoryService;

    public function __construct(TaskService $taskService, CategoryService $categoryService)
    {
        $this->taskService = $taskService;
        $this->categoryService = $categoryService;
    }

    /**
     * Obtient le libellé d'un statut.
     */
    public function getStatusLabel(string $status): string
    {
        $statuses = config('task-manager.statuses', []);
        return $statuses[$status]['name'] ?? $status;
    }

    /**
     * Obtient le libellé d'une priorité.
     */
    public function getPriorityLabel(string $priority): string
    {
        $priorities = config('task-manager.priorities', []);
        return $priorities[$priority]['name'] ?? $priority;
    }

    /**
     * Formate une date d'échéance.
     */
    public function formatDueDate($date): string
    {
        if (!$date) return '';
        return \Carbon\Carbon::parse($date)->format('d/m/Y H:i');
    }

    /**
     * Vérifie si une tâche est en retard.
     */
    public function isOverdue($task): bool
    {
        if (!$task->due_date) return false;
        return $task->due_date->isPast() && $task->status !== 'completed';
    }

    /**
     * Obtient les statistiques du plugin.
     */
    public function getStats(): array
    {
        return $this->taskService->getTaskStats();
    }

    /**
     * Obtient les tâches en retard.
     */
    public function getOverdueTasks(int $limit = null): Collection
    {
        return $this->taskService->getOverdueTasks($limit);
    }

    /**
     * Obtient les catégories actives.
     */
    public function getActiveCategories(): Collection
    {
        return Category::active()->ordered()->get();
    }
}
