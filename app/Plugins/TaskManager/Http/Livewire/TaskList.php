<?php

namespace App\Plugins\TaskManager\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Plugins\TaskManager\Models\Task;
use App\Plugins\TaskManager\Models\Category;
use App\Plugins\TaskManager\Services\TaskService;
use App\Plugins\TaskManager\Services\ExportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Composant Livewire pour la liste des tâches
 *
 * Ce composant gère l'affichage interactif de la liste des tâches
 * avec filtres, tri, pagination et actions en temps réel.
 */
class TaskList extends Component
{
    use WithPagination, WithFileUploads;

    /**
     * Le service de gestion des tâches.
     */
    protected TaskService $taskService;

    /**
     * Filtres de recherche.
     */
    public $search = '';
    public $status = '';
    public $priority = '';
    public $assignedTo = '';
    public $categoryId = '';
    public $dueDateFrom = '';
    public $dueDateTo = '';
    public $createdBy = '';
    public $isPublic = '';

    /**
     * Tri et pagination.
     */
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;

    /**
     * Actions en lot.
     */
    public $selectedTasks = [];
    public $selectAll = false;

    /**
     * État de l'interface.
     */
    public $showFilters = false;
    public $viewMode = 'list'; // list, grid, kanban
    public $loading = false;

    /**
     * Constructeur du composant.
     */
    public function mount(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Règles de validation.
     */
    protected function rules()
    {
        return [
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,in_progress,completed,cancelled',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'assignedTo' => 'nullable|integer|exists:users,id',
            'categoryId' => 'nullable|integer|exists:task_categories,id',
            'dueDateFrom' => 'nullable|date',
            'dueDateTo' => 'nullable|date|after_or_equal:dueDateFrom',
            'createdBy' => 'nullable|integer|exists:users,id',
            'isPublic' => 'nullable|boolean',
            'sortBy' => 'nullable|string',
            'sortDirection' => 'nullable|string|in:asc,desc',
            'perPage' => 'nullable|integer|min:5|max:100',
        ];
    }

    /**
     * Rendu du composant.
     */
    public function render()
    {
        // Vérification des permissions
        if (!Gate::allows('view_tasks')) {
            abort(403);
        }

        // Récupération des filtres
        $filters = $this->getFilters();

        // Récupération des tâches
        $tasks = $this->taskService->getTasksWithFilters($filters, $this->perPage);

        // Récupération des données pour les filtres
        $categories = Category::active()->ordered()->get();
        $users = \App\Models\User::orderBy('name')->get();

        // Statistiques
        $stats = $this->taskService->getTaskStats();

        return view('task-manager::livewire.task-list', compact('tasks', 'categories', 'users', 'stats'));
    }

    /**
     * Obtient les filtres actuels.
     */
    protected function getFilters(): array
    {
        return array_filter([
            'search' => $this->search,
            'status' => $this->status,
            'priority' => $this->priority,
            'assigned_to' => $this->assignedTo,
            'category_id' => $this->categoryId,
            'due_date_from' => $this->dueDateFrom,
            'due_date_to' => $this->dueDateTo,
            'created_by' => $this->createdBy,
            'is_public' => $this->isPublic,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection,
        ]);
    }

    /**
     * Applique les filtres.
     */
    public function applyFilters()
    {
        $this->validate();
        $this->resetPage();
        $this->loading = true;
    }

    /**
     * Réinitialise les filtres.
     */
    public function resetFilters()
    {
        $this->reset([
            'search', 'status', 'priority', 'assignedTo', 'categoryId',
            'dueDateFrom', 'dueDateTo', 'createdBy', 'isPublic'
        ]);
        $this->resetPage();
        $this->loading = true;
    }

    /**
     * Change le mode d'affichage.
     */
    public function changeViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->loading = true;
    }

    /**
     * Change le tri.
     */
    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        $this->loading = true;
    }

    /**
     * Change le nombre d'éléments par page.
     */
    public function changePerPage($perPage)
    {
        $this->perPage = $perPage;
        $this->resetPage();
        $this->loading = true;
    }

    /**
     * Sélectionne/désélectionne toutes les tâches.
     */
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedTasks = $this->getTaskIds();
        } else {
            $this->selectedTasks = [];
        }
    }

    /**
     * Obtient les IDs des tâches actuelles.
     */
    protected function getTaskIds(): array
    {
        $filters = $this->getFilters();
        $tasks = $this->taskService->getTasksWithFilters($filters, $this->perPage);
        return $tasks->pluck('id')->toArray();
    }

    /**
     * Change le statut des tâches sélectionnées.
     */
    public function changeStatusForSelected($status)
    {
        if (empty($this->selectedTasks)) {
            $this->addError('selectedTasks', 'Aucune tâche sélectionnée.');
            return;
        }

        if (!Gate::allows('edit_tasks')) {
            $this->addError('permission', 'Permission refusée.');
            return;
        }

        try {
            foreach ($this->selectedTasks as $taskId) {
                $task = Task::find($taskId);
                if ($task && Gate::allows('update', $task)) {
                    $this->taskService->changeTaskStatus($task, $status);
                }
            }

            $this->selectedTasks = [];
            $this->selectAll = false;
            $this->loading = true;

            session()->flash('success', 'Statut mis à jour pour les tâches sélectionnées.');
        } catch (\Exception $e) {
            $this->addError('status', 'Erreur lors de la mise à jour du statut: ' . $e->getMessage());
        }
    }

    /**
     * Change la priorité des tâches sélectionnées.
     */
    public function changePriorityForSelected($priority)
    {
        if (empty($this->selectedTasks)) {
            $this->addError('selectedTasks', 'Aucune tâche sélectionnée.');
            return;
        }

        if (!Gate::allows('edit_tasks')) {
            $this->addError('permission', 'Permission refusée.');
            return;
        }

        try {
            foreach ($this->selectedTasks as $taskId) {
                $task = Task::find($taskId);
                if ($task && Gate::allows('update', $task)) {
                    $this->taskService->changeTaskPriority($task, $priority);
                }
            }

            $this->selectedTasks = [];
            $this->selectAll = false;
            $this->loading = true;

            session()->flash('success', 'Priorité mise à jour pour les tâches sélectionnées.');
        } catch (\Exception $e) {
            $this->addError('priority', 'Erreur lors de la mise à jour de la priorité: ' . $e->getMessage());
        }
    }

    /**
     * Assigne les tâches sélectionnées à un utilisateur.
     */
    public function assignSelectedToUser($userId)
    {
        if (empty($this->selectedTasks)) {
            $this->addError('selectedTasks', 'Aucune tâche sélectionnée.');
            return;
        }

        if (!Gate::allows('assign_tasks')) {
            $this->addError('permission', 'Permission refusée.');
            return;
        }

        try {
            foreach ($this->selectedTasks as $taskId) {
                $task = Task::find($taskId);
                if ($task && Gate::allows('assign', $task)) {
                    $this->taskService->assignTask($task, $userId);
                }
            }

            $this->selectedTasks = [];
            $this->selectAll = false;
            $this->loading = true;

            session()->flash('success', 'Tâches assignées avec succès.');
        } catch (\Exception $e) {
            $this->addError('assignment', 'Erreur lors de l\'assignation: ' . $e->getMessage());
        }
    }

    /**
     * Supprime les tâches sélectionnées.
     */
    public function deleteSelected()
    {
        if (empty($this->selectedTasks)) {
            $this->addError('selectedTasks', 'Aucune tâche sélectionnée.');
            return;
        }

        if (!Gate::allows('delete_tasks')) {
            $this->addError('permission', 'Permission refusée.');
            return;
        }

        try {
            foreach ($this->selectedTasks as $taskId) {
                $task = Task::find($taskId);
                if ($task && Gate::allows('delete', $task)) {
                    $this->taskService->deleteTask($task);
                }
            }

            $this->selectedTasks = [];
            $this->selectAll = false;
            $this->loading = true;

            session()->flash('success', 'Tâches supprimées avec succès.');
        } catch (\Exception $e) {
            $this->addError('delete', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Exporte les tâches filtrées.
     */
    public function exportTasks($format = 'xlsx')
    {
        if (!Gate::allows('export_tasks')) {
            $this->addError('permission', 'Permission refusée.');
            return;
        }

        try {
            $filters = $this->getFilters();
            $exportService = app(\App\Plugins\TaskManager\Services\ExportService::class);

            return $exportService->exportTasks($format, $filters);
        } catch (\Exception $e) {
            $this->addError('export', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Rafraîchit la liste.
     */
    public function refresh()
    {
        $this->loading = true;
        $this->emit('taskListRefreshed');
    }

    /**
     * Écoute les événements de mise à jour.
     */
    protected function getListeners()
    {
        return [
            'taskUpdated' => 'refresh',
            'taskCreated' => 'refresh',
            'taskDeleted' => 'refresh',
            'echo:task-manager,TaskUpdated' => 'refresh',
            'echo:task-manager,TaskCreated' => 'refresh',
            'echo:task-manager,TaskDeleted' => 'refresh',
        ];
    }

    /**
     * Hook appelé après la mise à jour des propriétés.
     */
    public function updated($propertyName)
    {
        // Réinitialiser la pagination lors du changement de filtres
        if (in_array($propertyName, ['search', 'status', 'priority', 'assignedTo', 'categoryId', 'dueDateFrom', 'dueDateTo', 'createdBy', 'isPublic'])) {
            $this->resetPage();
        }
    }

    /**
     * Hook appelé après le rendu.
     */
    public function dehydrate()
    {
        $this->loading = false;
    }
}
