<?php

namespace App\Plugins\TaskManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request de validation pour les tâches
 *
 * Cette classe gère la validation des données lors de la création
 * et de la modification des tâches.
 */
class TaskRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return true; // La vérification des permissions se fait dans le contrôleur
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        $taskId = $this->route('task')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', 'string', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'priority' => ['required', 'string', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'category_id' => ['nullable', 'integer', 'exists:task_categories,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'actual_hours' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_public' => ['boolean'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:tasks,id',
                function ($attribute, $value, $fail) use ($taskId) {
                    if ($value == $taskId) {
                        $fail('Une tâche ne peut pas être sa propre sous-tâche.');
                    }
                }
            ],
            'dependencies' => ['nullable', 'array'],
            'dependencies.*' => ['integer', 'exists:tasks,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif'],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre de la tâche est requis.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.max' => 'La description ne peut pas dépasser 10000 caractères.',
            'status.in' => 'Le statut sélectionné n\'est pas valide.',
            'priority.in' => 'La priorité sélectionnée n\'est pas valide.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'assigned_to.exists' => 'L\'utilisateur assigné n\'existe pas.',
            'due_date.after_or_equal' => 'La date d\'échéance doit être aujourd\'hui ou dans le futur.',
            'estimated_hours.min' => 'Les heures estimées ne peuvent pas être négatives.',
            'estimated_hours.max' => 'Les heures estimées ne peuvent pas dépasser 1000.',
            'actual_hours.min' => 'Les heures réelles ne peuvent pas être négatives.',
            'actual_hours.max' => 'Les heures réelles ne peuvent pas dépasser 1000.',
            'progress.min' => 'Le progrès ne peut pas être inférieur à 0%.',
            'progress.max' => 'Le progrès ne peut pas dépasser 100%.',
            'parent_id.exists' => 'La tâche parent sélectionnée n\'existe pas.',
            'dependencies.*.exists' => 'Une des tâches de dépendance n\'existe pas.',
            'tags.*.max' => 'Les étiquettes ne peuvent pas dépasser 50 caractères.',
            'attachments.*.file' => 'Les pièces jointes doivent être des fichiers.',
            'attachments.*.max' => 'Les pièces jointes ne peuvent pas dépasser 10MB.',
            'attachments.*.mimes' => 'Les pièces jointes doivent être au format PDF, DOC, DOCX, TXT, JPG, JPEG, PNG ou GIF.',
        ];
    }

    /**
     * Attributs personnalisés pour les messages d'erreur.
     */
    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'description' => 'description',
            'status' => 'statut',
            'priority' => 'priorité',
            'category_id' => 'catégorie',
            'assigned_to' => 'assigné à',
            'due_date' => 'date d\'échéance',
            'estimated_hours' => 'heures estimées',
            'actual_hours' => 'heures réelles',
            'progress' => 'progrès',
            'is_public' => 'publique',
            'parent_id' => 'tâche parent',
            'dependencies' => 'dépendances',
            'tags' => 'étiquettes',
            'attachments' => 'pièces jointes',
        ];
    }

    /**
     * Prépare les données pour la validation.
     */
    protected function prepareForValidation(): void
    {
        // Convertir les valeurs booléennes
        $this->merge([
            'is_public' => $this->boolean('is_public'),
            'progress' => $this->input('progress') ? (int) $this->input('progress') : 0,
        ]);

        // Nettoyer les étiquettes
        if ($this->has('tags')) {
            $tags = array_filter(array_map('trim', $this->input('tags', [])));
            $this->merge(['tags' => $tags]);
        }
    }
}
