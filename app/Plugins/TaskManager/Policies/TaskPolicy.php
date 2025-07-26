<?php

namespace App\Plugins\TaskManager\Policies;

use App\Models\User;
use App\Plugins\TaskManager\Models\Task;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy pour les tâches
 */
class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir toutes les tâches.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_tasks');
    }

    /**
     * Détermine si l'utilisateur peut voir une tâche spécifique.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->can('view_tasks') &&
               ($task->is_public || $user->id === $task->created_by || $user->id === $task->assigned_to);
    }

    /**
     * Détermine si l'utilisateur peut créer des tâches.
     */
    public function create(User $user): bool
    {
        return $user->can('create_tasks');
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour une tâche.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->can('edit_tasks') &&
               ($user->id === $task->created_by || $user->id === $task->assigned_to);
    }

    /**
     * Détermine si l'utilisateur peut supprimer une tâche.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->can('delete_tasks') && $user->id === $task->created_by;
    }

    /**
     * Détermine si l'utilisateur peut assigner une tâche.
     */
    public function assign(User $user, Task $task): bool
    {
        return $user->can('assign_tasks');
    }
}
