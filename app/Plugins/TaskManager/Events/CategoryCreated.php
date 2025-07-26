<?php

namespace App\Plugins\TaskManager\Events;

use App\Plugins\TaskManager\Models\Category;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Événement déclenché lors de la création d'une catégorie
 */
class CategoryCreated
{
    use Dispatchable, SerializesModels;

    public Category $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }
}
