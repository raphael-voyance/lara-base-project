<?php

namespace App\Plugins\TaskManager\Repositories;

use App\Plugins\TaskManager\Models\Category;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository pour la gestion des catégories
 */
class CategoryRepository
{
    protected Category $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    /**
     * Récupère toutes les catégories.
     */
    public function getAll(): Collection
    {
        return $this->model->ordered()->get();
    }

    /**
     * Récupère les catégories actives.
     */
    public function getActive(): Collection
    {
        return $this->model->active()->ordered()->get();
    }

    /**
     * Trouve une catégorie par son ID.
     */
    public function findById(int $id): ?Category
    {
        return $this->model->find($id);
    }

    /**
     * Crée une nouvelle catégorie.
     */
    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    /**
     * Met à jour une catégorie.
     */
    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    /**
     * Supprime une catégorie.
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
