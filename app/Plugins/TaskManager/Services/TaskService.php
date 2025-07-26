<?php

namespace App\Plugins\TaskManager\Services;

use App\Plugins\TaskManager\Models\Task;
use App\Plugins\TaskManager\Models\Category;
use App\Plugins\TaskManager\Repositories\TaskRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service de gestion des tâches
 *
 * Ce service encapsule toute la logique métier liée aux tâches,
 * incluant la création, modification, suppression et récupération
 * des tâches avec gestion des permissions.
 */
class TaskService
{
    protected TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Récupère une liste paginée de tâches avec filtres.
     */
    public function getTasks(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->taskRepository->getTasks($filters, $perPage);
    }

    /**
     * Récupère une liste paginée de tâches avec filtres (alias pour compatibilité).
     */
    public function getTasksWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->getTasks($filters, $perPage);
    }

    /**
     * Récupère une tâche par son ID.
     */
    public function getTask(int $id): ?Task
    {
        return $this->taskRepository->findById($id);
    }

    /**
     * Crée une nouvelle tâche.
     */
    public function createTask(array $data): Task
    {
        $data['created_by'] = Auth::id();

        return $this->taskRepository->create($data);
    }

    /**
     * Met à jour une tâche existante.
     */
    public function updateTask(Task $task, array $data): bool
    {
        return $this->taskRepository->update($task, $data);
    }

    /**
     * Supprime une tâche.
     */
    public function deleteTask(Task $task): bool
    {
        return $this->taskRepository->delete($task);
    }

    /**
     * Change le statut d'une tâche.
     */
    public function changeStatus(Task $task, string $status): bool
    {
        return $this->taskRepository->update($task, ['status' => $status]);
    }

    /**
     * Change le statut d'une tâche (alias pour compatibilité).
     */
    public function changeTaskStatus(Task $task, string $status): bool
    {
        return $this->changeStatus($task, $status);
    }

    /**
     * Change la priorité d'une tâche.
     */
    public function changePriority(Task $task, string $priority): bool
    {
        return $this->taskRepository->update($task, ['priority' => $priority]);
    }

    /**
     * Change la priorité d'une tâche (alias pour compatibilité).
     */
    public function changeTaskPriority(Task $task, string $priority): bool
    {
        return $this->changePriority($task, $priority);
    }

    /**
     * Assigne une tâche à un utilisateur.
     */
    public function assignTask(Task $task, int $userId): bool
    {
        return $this->taskRepository->update($task, ['assigned_to' => $userId]);
    }

    /**
     * Met à jour le progrès d'une tâche.
     */
    public function updateProgress(Task $task, int $progress): bool
    {
        return $this->taskRepository->update($task, ['progress' => $progress]);
    }

    /**
     * Duplique une tâche.
     */
    public function duplicateTask(Task $task): Task
    {
        $data = $task->toArray();
        unset($data['id'], $data['created_at'], $data['updated_at']);
        $data['title'] = $data['title'] . ' (Copie)';
        $data['status'] = 'pending';
        $data['progress'] = 0;

        return $this->createTask($data);
    }

    /**
     * Récupère les statistiques des tâches.
     */
    public function getTaskStats(Task|null $task = null): array
    {
        if ($task) {
            return [
                'total_comments' => $task->comments()->count(),
                'total_attachments' => $task->attachments()->count(),
                'total_subtasks' => $task->subtasks()->count(),
                'total_dependencies' => $task->dependencies()->count(),
                'time_spent' => $task->timeEntries()->sum('hours'),
                'days_remaining' => $task->due_date ? now()->diffInDays($task->due_date, false) : null,
            ];
        }

        return $this->taskRepository->getStats();
    }

    /**
     * Récupère les tâches en retard.
     */
    public function getOverdueTasks(int|null $limit = null): Collection
    {
        return $this->taskRepository->getOverdueTasks($limit);
    }

    /**
     * Récupère les tâches du jour.
     */
    public function getTodayTasks(): Collection
    {
        return $this->taskRepository->getTodayTasks();
    }

    /**
     * Récupère les tâches de la semaine.
     */
    public function getWeekTasks(): Collection
    {
        return $this->taskRepository->getWeekTasks();
    }

    /**
     * Ajoute des dépendances à une tâche.
     */
    public function addDependencies(Task $task, array $dependencyIds): void
    {
        $task->dependencies()->sync($dependencyIds);
    }

    /**
     * Met à jour les dépendances d'une tâche.
     */
    public function updateDependencies(Task $task, array $dependencyIds): void
    {
        $task->dependencies()->sync($dependencyIds);
    }

    /**
     * Ajoute des fichiers attachés à une tâche.
     */
    public function addAttachments(Task $task, array $files): void
    {
        foreach ($files as $file) {
            $path = $file->store('task-attachments', 'public');
            $task->attachments()->create([
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => Auth::id(),
            ]);
        }
    }

    /**
     * Récupère les tâches liées.
     */
    public function getRelatedTasks(Task $task): Collection
    {
        return Task::where('category_id', $task->category_id)
            ->where('id', '!=', $task->id)
            ->limit(5)
            ->get();
    }

    /**
     * Vérifie si un statut est valide.
     */
    public function isValidStatus(string $status): bool
    {
        return in_array($status, ['pending', 'in_progress', 'completed', 'cancelled']);
    }

    /**
     * Vérifie si une priorité est valide.
     */
    public function isValidPriority(string $priority): bool
    {
        return in_array($priority, ['low', 'medium', 'high', 'urgent']);
    }

    /**
     * Met à jour le progrès d'une tâche.
     */
    public function updateTaskProgress(Task $task, int $progress): bool
    {
        return $this->updateProgress($task, $progress);
    }

    /**
     * Récupère les statistiques du tableau de bord.
     */
    public function getDashboardStats(): array
    {
        return [
            'total_tasks' => $this->taskRepository->count(),
            'completed_tasks' => $this->taskRepository->countByStatus('completed'),
            'pending_tasks' => $this->taskRepository->countByStatus('pending'),
            'overdue_tasks' => $this->taskRepository->countOverdue(),
            'tasks_due_today' => $this->taskRepository->getDueTodayTasks()->count(),
        ];
    }

    /**
     * Récupère les tâches récentes.
     */
    public function getRecentTasks(int $limit = 10): Collection
    {
        return $this->taskRepository->getRecentTasks($limit);
    }

    /**
     * Récupère les tâches dues aujourd'hui.
     */
    public function getDueTodayTasks(int $limit = 5): Collection
    {
        return $this->taskRepository->getDueTodayTasks($limit);
    }

    /**
     * Récupère les tâches par statut.
     */
    public function getTasksByStatus(): array
    {
        return $this->taskRepository->getCountByStatus();
    }

    /**
     * Récupère les tâches par priorité.
     */
    public function getTasksByPriority(): array
    {
        return $this->taskRepository->getCountByPriority();
    }

    /**
     * Récupère les tâches pour le calendrier.
     */
    public function getTasksForCalendar(): Collection
    {
        return $this->taskRepository->getTasksForCalendar();
    }

    /**
     * Récupère les données pour les rapports.
     */
    public function getReportData(): array
    {
        return [
            'stats' => $this->getTaskStats(),
            'tasks_by_status' => $this->getTasksByStatus(),
            'tasks_by_priority' => $this->getTasksByPriority(),
            'overdue_tasks' => $this->getOverdueTasks(),
            'recent_activities' => $this->taskRepository->getRecentActivities(),
        ];
    }
}
