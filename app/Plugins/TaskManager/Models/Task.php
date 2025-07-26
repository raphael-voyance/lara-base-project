<?php

namespace App\Plugins\TaskManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * Modèle Task - Gestion des tâches
 *
 * Ce modèle représente une tâche dans le système de gestion de tâches.
 * Il inclut toutes les relations, scopes et méthodes nécessaires pour
 * une gestion complète des tâches.
 */
class Task extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'completed_at',
        'assigned_to',
        'created_by',
        'category_id',
        'estimated_hours',
        'actual_hours',
        'progress',
        'is_public',
        'tags',
        'attachments',
        'notes',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'progress' => 'integer',
        'is_public' => 'boolean',
        'tags' => 'array',
        'attachments' => 'array',
        'notes' => 'array',
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
        'is_overdue',
        'is_due_today',
        'is_due_this_week',
        'status_label',
        'priority_label',
        'formatted_due_date',
        'formatted_created_at',
        'formatted_completed_at',
    ];

    /**
     * Relation avec l'utilisateur créateur de la tâche.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur assigné à la tâche.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    /**
     * Relation avec la catégorie de la tâche.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relation avec les commentaires de la tâche.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    /**
     * Relation avec les fichiers attachés à la tâche.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class);
    }

    /**
     * Relation avec les étiquettes de la tâche.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(TaskTag::class, 'task_tag');
    }

    /**
     * Relation avec les sous-tâches.
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    /**
     * Relation avec la tâche parente.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /**
     * Relation avec les dépendances (tâches qui doivent être terminées avant).
     */
    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'dependency_id');
    }

    /**
     * Relation avec les tâches qui dépendent de celle-ci.
     */
    public function dependents(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'dependency_id', 'task_id');
    }

    /**
     * Relation avec les activités de la tâche.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(TaskActivity::class);
    }

    /**
     * Relation avec les rapports de temps.
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TaskTimeEntry::class);
    }

    /**
     * Scope pour filtrer les tâches par statut.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour filtrer les tâches par priorité.
     */
    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope pour filtrer les tâches assignées à un utilisateur.
     */
    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope pour filtrer les tâches créées par un utilisateur.
     */
    public function scopeCreatedBy(Builder $query, int $userId): Builder
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope pour filtrer les tâches en retard.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'completed');
    }

    /**
     * Scope pour filtrer les tâches dues aujourd'hui.
     */
    public function scopeDueToday(Builder $query): Builder
    {
        return $query->whereDate('due_date', today());
    }

    /**
     * Scope pour filtrer les tâches dues cette semaine.
     */
    public function scopeDueThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('due_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope pour filtrer les tâches publiques.
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope pour filtrer les tâches privées.
     */
    public function scopePrivate(Builder $query): Builder
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope pour filtrer les tâches par catégorie.
     */
    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope pour rechercher dans les tâches.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%");
        });
    }

    /**
     * Vérifie si la tâche est en retard.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               $this->status !== 'completed';
    }

    /**
     * Vérifie si la tâche est due aujourd'hui.
     */
    public function getIsDueTodayAttribute(): bool
    {
        return $this->due_date && $this->due_date->isToday();
    }

    /**
     * Vérifie si la tâche est due cette semaine.
     */
    public function getIsDueThisWeekAttribute(): bool
    {
        return $this->due_date && $this->due_date->isThisWeek();
    }

    /**
     * Obtient le libellé du statut.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = config('task-manager.statuses', []);
        return $statuses[$this->status]['name'] ?? $this->status;
    }

    /**
     * Obtient le libellé de la priorité.
     */
    public function getPriorityLabelAttribute(): string
    {
        $priorities = config('task-manager.priorities', []);
        return $priorities[$this->priority]['name'] ?? $this->priority;
    }

    /**
     * Obtient la couleur du statut.
     */
    public function getStatusColorAttribute(): string
    {
        $statuses = config('task-manager.statuses', []);
        return $statuses[$this->status]['color'] ?? 'gray';
    }

    /**
     * Obtient la couleur de la priorité.
     */
    public function getPriorityColorAttribute(): string
    {
        $priorities = config('task-manager.priorities', []);
        return $priorities[$this->priority]['color'] ?? 'gray';
    }

    /**
     * Obtient la date d'échéance formatée.
     */
    public function getFormattedDueDateAttribute(): ?string
    {
        return $this->due_date ? $this->due_date->format('d/m/Y H:i') : null;
    }

    /**
     * Obtient la date de création formatée.
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Obtient la date de completion formatée.
     */
    public function getFormattedCompletedAtAttribute(): ?string
    {
        return $this->completed_at ? $this->completed_at->format('d/m/Y H:i') : null;
    }

    /**
     * Marque la tâche comme terminée.
     */
    public function markAsCompleted(): bool
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'progress' => 100,
        ]);

        return true;
    }

    /**
     * Marque la tâche comme en cours.
     */
    public function markAsInProgress(): bool
    {
        $this->update([
            'status' => 'in_progress',
            'progress' => max($this->progress, 10),
        ]);

        return true;
    }

    /**
     * Met à jour le progrès de la tâche.
     */
    public function updateProgress(int $progress): bool
    {
        $progress = max(0, min(100, $progress));

        $this->update(['progress' => $progress]);

        if ($progress === 100) {
            $this->markAsCompleted();
        }

        return true;
    }

    /**
     * Assigne la tâche à un utilisateur.
     */
    public function assignTo(int $userId): bool
    {
        $this->update(['assigned_to' => $userId]);
        return true;
    }

    /**
     * Change la priorité de la tâche.
     */
    public function changePriority(string $priority): bool
    {
        $priorities = config('task-manager.priorities', []);

        if (array_key_exists($priority, $priorities)) {
            $this->update(['priority' => $priority]);
            return true;
        }

        return false;
    }

    /**
     * Change le statut de la tâche.
     */
    public function changeStatus(string $status): bool
    {
        $statuses = config('task-manager.statuses', []);

        if (array_key_exists($status, $statuses)) {
            $this->update(['status' => $status]);
            return true;
        }

        return false;
    }

    /**
     * Ajoute un commentaire à la tâche.
     */
    public function addComment(string $content, int $userId): TaskComment
    {
        return $this->comments()->create([
            'content' => $content,
            'user_id' => $userId,
        ]);
    }

    /**
     * Ajoute un fichier attaché à la tâche.
     */
    public function addAttachment(string $filename, string $path, int $userId): TaskAttachment
    {
        return $this->attachments()->create([
            'filename' => $filename,
            'path' => $path,
            'user_id' => $userId,
        ]);
    }

    /**
     * Calcule le temps total passé sur la tâche.
     */
    public function getTotalTimeSpent(): float
    {
        return $this->timeEntries()->sum('duration');
    }

    /**
     * Vérifie si la tâche peut être supprimée.
     */
    public function canBeDeleted(): bool
    {
        return $this->dependents()->count() === 0;
    }

    /**
     * Vérifie si la tâche peut être terminée.
     */
    public function canBeCompleted(): bool
    {
        return $this->dependencies()->where('status', '!=', 'completed')->count() === 0;
    }

    /**
     * Boot du modèle.
     */
    protected static function boot()
    {
        parent::boot();

        // Événement de création
        static::created(function ($task) {
            event(new \App\Plugins\TaskManager\Events\TaskCreated($task));
        });

        // Événement de mise à jour
        static::updated(function ($task) {
            event(new \App\Plugins\TaskManager\Events\TaskUpdated($task));
        });

        // Événement de suppression
        static::deleted(function ($task) {
            event(new \App\Plugins\TaskManager\Events\TaskDeleted($task));
        });
    }
}
