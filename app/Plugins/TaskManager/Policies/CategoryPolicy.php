<?php

namespace App\Plugins\TaskManager\Policies;

use App\Models\User;
use App\Plugins\TaskManager\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy pour les catégories
 */
class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir toutes les catégories.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_tasks');
    }

    /**
     * Détermine si l'utilisateur peut voir une catégorie spécifique.
     */
    public function view(User $user, Category $category): bool
    {
        return $user->can('view_tasks');
    }

    /**
     * Détermine si l'utilisateur peut créer des catégories.
     */
    public function create(User $user): bool
    {
        return $user->can('manage_categories');
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour une catégorie.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->can('manage_categories');
    }

    /**
     * Détermine si l'utilisateur peut supprimer une catégorie.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->can('manage_categories') && !$category->hasTasks();
    }
}
