<?php

namespace App\Plugins\TaskManager\Repositories;

use App\Plugins\TaskManager\Models\Task;
use App\Plugins\TaskManager\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Repository pour la gestion des tâches
 *
 * Ce repository encapsule toute la logique d'accès aux données
 * pour les tâches, incluant les requêtes complexes et les filtres.
 */
class TaskRepository
{
    protected Task $model;

    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    /**
     * Récupère une liste paginée de tâches avec filtres.
     */
    public function getTasks(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['creator', 'assignee', 'category']);

        $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Récupère les tâches pour l'export.
     */
    public function getTasksForExport(array $filters = []): Collection
    {
        $query = $this->model->with(['creator', 'assignee', 'category']);

        $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Trouve une tâche par son ID.
     */
    public function findById(int $id): ?Task
    {
        return $this->model->with(['creator', 'assignee', 'category', 'comments', 'attachments'])->find($id);
    }

    /**
     * Crée une nouvelle tâche.
     */
    public function create(array $data): Task
    {
        return $this->model->create($data);
    }

    /**
     * Met à jour une tâche existante.
     */
    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    /**
     * Supprime une tâche.
     */
    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Applique les filtres à la requête.
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['assignedTo'])) {
            $query->where('assigned_to', $filters['assignedTo']);
        }

        if (!empty($filters['categoryId'])) {
            $query->where('category_id', $filters['categoryId']);
        }

        if (!empty($filters['dueDateFrom'])) {
            $query->where('due_date', '>=', $filters['dueDateFrom']);
        }

        if (!empty($filters['dueDateTo'])) {
            $query->where('due_date', '<=', $filters['dueDateTo']);
        }

        if (!empty($filters['createdBy'])) {
            $query->where('created_by', $filters['createdBy']);
        }

        if (isset($filters['isPublic'])) {
            $query->where('is_public', $filters['isPublic']);
        }
    }

    /**
     * Compte le nombre total de tâches.
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Compte les tâches par statut.
     */
    public function countByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }

    /**
     * Compte les tâches en retard.
     */
    public function countOverdue(): int
    {
        return $this->model->overdue()->count();
    }

    /**
     * Récupère les tâches en retard.
     */
    public function getOverdueTasks(int|null $limit = null): Collection
    {
        $query = $this->model->overdue()->with(['creator', 'assignee']);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Récupère les tâches du jour.
     */
    public function getTodayTasks(): Collection
    {
        return $this->model->whereDate('due_date', today())
                          ->with(['creator', 'assignee'])
                          ->get();
    }

    /**
     * Récupère les tâches de la semaine.
     */
    public function getWeekTasks(): Collection
    {
        return $this->model->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])
                          ->with(['creator', 'assignee'])
                          ->get();
    }

    /**
     * Récupère les statistiques des tâches.
     */
    public function getStats(): array
    {
        return [
            'total' => $this->count(),
            'pending' => $this->countByStatus('pending'),
            'in_progress' => $this->countByStatus('in_progress'),
            'completed' => $this->countByStatus('completed'),
            'cancelled' => $this->countByStatus('cancelled'),
            'overdue' => $this->countOverdue(),
        ];
    }

    /**
     * Récupère le nombre de tâches par priorité.
     */
    public function getCountByPriority(): array
    {
        return $this->model->selectRaw('priority, count(*) as count')
                          ->groupBy('priority')
                          ->pluck('count', 'priority')
                          ->toArray();
    }

    /**
     * Récupère le nombre de tâches par statut.
     */
    public function getCountByStatus(): array
    {
        return $this->model->selectRaw('status, count(*) as count')
                          ->groupBy('status')
                          ->pluck('count', 'status')
                          ->toArray();
    }

    /**
     * Récupère les tâches récentes.
     */
    public function getRecentTasks(int $limit = 10): Collection
    {
        return $this->model->with(['creator', 'assignee', 'category'])
                          ->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->get();
    }

    /**
     * Récupère les tâches dues aujourd'hui.
     */
    public function getDueTodayTasks(int $limit = 5): Collection
    {
        return $this->model->whereDate('due_date', today())
                          ->with(['creator', 'assignee'])
                          ->limit($limit)
                          ->get();
    }

    /**
     * Récupère les tâches pour le calendrier.
     */
    public function getTasksForCalendar(): Collection
    {
        return $this->model->whereNotNull('due_date')
                          ->with(['creator', 'assignee', 'category'])
                          ->get();
    }

    /**
     * Récupère les activités récentes.
     */
    public function getRecentActivities(): Collection
    {
        // Cette méthode nécessiterait une table d'activités
        // Pour l'instant, on retourne une collection vide
        return collect();
    }
}
