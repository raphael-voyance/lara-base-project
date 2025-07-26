<?php

namespace App\Plugins\TaskManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Modèle pour les étiquettes de tâches
 *
 * Ce modèle gère les étiquettes qui peuvent être assignées aux tâches
 * pour faciliter leur organisation et leur recherche.
 */
class TaskTag extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'name',
        'description',
        'color',
        'is_active',
        'created_by',
    ];

    /**
     * Les attributs à caster.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les attributs à masquer lors de la sérialisation.
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Les attributs à ajouter lors de la sérialisation.
     */
    protected $appends = [
        'task_count',
        'formatted_created_at',
    ];

    /**
     * Relation avec les tâches.
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_tag', 'tag_id', 'task_id')
                    ->withTimestamps();
    }

    /**
     * Relation avec l'utilisateur créateur.
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Scope pour les étiquettes actives.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les étiquettes inactives.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope pour rechercher par nom.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Accesseur pour le nombre de tâches.
     */
    public function getTaskCountAttribute(): int
    {
        return $this->tasks()->count();
    }

    /**
     * Accesseur pour la date de création formatée.
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Active l'étiquette.
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Désactive l'étiquette.
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Vérifie si l'étiquette est utilisée.
     */
    public function isUsed(): bool
    {
        return $this->tasks()->exists();
    }

    /**
     * Boot du modèle.
     */
    protected static function boot()
    {
        parent::boot();

        // Avant la suppression, vérifier si l'étiquette est utilisée
        static::deleting(function ($tag) {
            if ($tag->isUsed()) {
                throw new \Exception('Impossible de supprimer une étiquette utilisée.');
            }
        });
    }
}
