<?php

namespace App\Plugins\TaskManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Modèle Category - Gestion des catégories de tâches
 *
 * Ce modèle représente une catégorie dans le système de gestion de tâches.
 * Il permet d'organiser les tâches par catégories pour une meilleure gestion.
 */
class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'task_categories';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'color',
        'icon',
        'is_active',
        'parent_id',
        'sort_order',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Les attributs qui doivent être cachés lors de la sérialisation.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Les attributs qui doivent être ajoutés lors de la sérialisation.
     *
     * @var array
     */
    protected $appends = [
        'task_count',
        'formatted_created_at',
    ];

    /**
     * Relation avec les tâches de cette catégorie.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Relation avec les sous-catégories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Relation avec la catégorie parente.
     */
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Scope pour filtrer les catégories actives.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer les catégories racines (sans parent).
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope pour trier par ordre de tri.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Scope pour rechercher dans les catégories.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Obtient le nombre de tâches dans cette catégorie.
     */
    public function getTaskCountAttribute(): int
    {
        return $this->tasks()->count();
    }

    /**
     * Obtient la date de création formatée.
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Vérifie si la catégorie a des sous-catégories.
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Vérifie si la catégorie a des tâches.
     */
    public function hasTasks(): bool
    {
        return $this->tasks()->count() > 0;
    }

    /**
     * Obtient toutes les sous-catégories récursivement.
     */
    public function getAllChildren(): \Illuminate\Support\Collection
    {
        $children = collect();

        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }

        return $children;
    }

    /**
     * Obtient tous les parents récursivement.
     */
    public function getAllParents(): \Illuminate\Support\Collection
    {
        $parents = collect();

        if ($this->parent) {
            $parents->push($this->parent);
            $parents = $parents->merge($this->parent->getAllParents());
        }

        return $parents;
    }

    /**
     * Obtient le chemin complet de la catégorie.
     */
    public function getFullPath(): string
    {
        $path = collect([$this->name]);
        $current = $this;

        while ($current->parent) {
            $current = $current->parent;
            $path->prepend($current->name);
        }

        return $path->implode(' > ');
    }

    /**
     * Active la catégorie.
     */
    public function activate(): bool
    {
        $this->update(['is_active' => true]);
        return true;
    }

    /**
     * Désactive la catégorie.
     */
    public function deactivate(): bool
    {
        $this->update(['is_active' => false]);
        return true;
    }

    /**
     * Change l'ordre de tri de la catégorie.
     */
    public function changeSortOrder(int $sortOrder): bool
    {
        $this->update(['sort_order' => $sortOrder]);
        return true;
    }

    /**
     * Vérifie si la catégorie peut être supprimée.
     */
    public function canBeDeleted(): bool
    {
        return !$this->hasTasks() && !$this->hasChildren();
    }

    /**
     * Boot du modèle.
     */
    protected static function boot()
    {
        parent::boot();

        // Événement de création
        static::created(function ($category) {
            event(new \App\Plugins\TaskManager\Events\CategoryCreated($category));
        });

        // Événement de mise à jour
        static::updated(function ($category) {
            event(new \App\Plugins\TaskManager\Events\CategoryUpdated($category));
        });

        // Événement de suppression
        static::deleted(function ($category) {
            event(new \App\Plugins\TaskManager\Events\CategoryDeleted($category));
        });
    }
}
