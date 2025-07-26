<?php

namespace App\Plugins\TaskManager\Services;

use App\Plugins\TaskManager\Models\Category;
use App\Plugins\TaskManager\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service de gestion des catégories
 */
class CategoryService
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Récupère toutes les catégories actives.
     */
    public function getActiveCategories(): Collection
    {
        return Category::active()->ordered()->get();
    }

    /**
     * Crée une nouvelle catégorie.
     */
    public function createCategory(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Met à jour une catégorie.
     */
    public function updateCategory(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    /**
     * Supprime une catégorie.
     */
    public function deleteCategory(Category $category): bool
    {
        if ($category->hasTasks()) {
            throw new \Exception('Impossible de supprimer une catégorie contenant des tâches.');
        }

        return $category->delete();
    }
}
