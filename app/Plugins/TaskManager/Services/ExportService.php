<?php

namespace App\Plugins\TaskManager\Services;

use App\Plugins\TaskManager\Models\Task;
use App\Plugins\TaskManager\Repositories\TaskRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Service d'export des tâches
 *
 * Ce service gère l'export des tâches dans différents formats
 * (Excel, CSV, PDF) avec filtres et personnalisation.
 */
class ExportService
{
    protected TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Exporte les tâches dans le format spécifié.
     */
    public function exportTasks(string $format, array $filters = []): StreamedResponse
    {
        $tasks = $this->taskRepository->getTasksForExport($filters);

        switch ($format) {
            case 'xlsx':
                return $this->exportToExcel($tasks);
            case 'csv':
                return $this->exportToCsv($tasks);
            case 'pdf':
                return $this->exportToPdf($tasks);
            default:
                throw new \InvalidArgumentException('Format d\'export non supporté: ' . $format);
        }
    }

    /**
     * Exporte vers Excel.
     */
    protected function exportToExcel($tasks): StreamedResponse
    {
        $filename = 'tasks_' . date('Y-m-d_H-i-s') . '.xlsx';

        return response()->stream(function () use ($tasks) {
            $handle = fopen('php://output', 'w');

            // En-têtes
            fputcsv($handle, [
                'ID', 'Titre', 'Description', 'Statut', 'Priorité',
                'Assigné à', 'Créé par', 'Date de création', 'Date d\'échéance',
                'Progrès', 'Catégorie'
            ]);

            // Données
            foreach ($tasks as $task) {
                fputcsv($handle, [
                    $task->id,
                    $task->title,
                    $task->description,
                    $task->status,
                    $task->priority,
                    $task->assignee ? $task->assignee->name : '',
                    $task->creator ? $task->creator->name : '',
                    $task->created_at->format('Y-m-d H:i:s'),
                    $task->due_date ? $task->due_date->format('Y-m-d') : '',
                    $task->progress . '%',
                    $task->category ? $task->category->name : ''
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Exporte vers CSV.
     */
    protected function exportToCsv($tasks): StreamedResponse
    {
        $filename = 'tasks_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->stream(function () use ($tasks) {
            $handle = fopen('php://output', 'w');

            // En-têtes
            fputcsv($handle, [
                'ID', 'Titre', 'Description', 'Statut', 'Priorité',
                'Assigné à', 'Créé par', 'Date de création', 'Date d\'échéance',
                'Progrès', 'Catégorie'
            ]);

            // Données
            foreach ($tasks as $task) {
                fputcsv($handle, [
                    $task->id,
                    $task->title,
                    $task->description,
                    $task->status,
                    $task->priority,
                    $task->assignee ? $task->assignee->name : '',
                    $task->creator ? $task->creator->name : '',
                    $task->created_at->format('Y-m-d H:i:s'),
                    $task->due_date ? $task->due_date->format('Y-m-d') : '',
                    $task->progress . '%',
                    $task->category ? $task->category->name : ''
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Exporte vers PDF.
     */
    protected function exportToPdf($tasks): StreamedResponse
    {
        $filename = 'tasks_' . date('Y-m-d_H-i-s') . '.pdf';

        // Pour l'instant, on retourne un CSV comme fallback
        // En production, vous utiliseriez une bibliothèque comme DomPDF ou Snappy
        return $this->exportToCsv($tasks);
    }

    /**
     * Génère un rapport de statistiques.
     */
    public function generateStatsReport(): array
    {
        return [
            'total_tasks' => $this->taskRepository->count(),
            'completed_tasks' => $this->taskRepository->countByStatus('completed'),
            'pending_tasks' => $this->taskRepository->countByStatus('pending'),
            'overdue_tasks' => $this->taskRepository->countOverdue(),
            'tasks_by_priority' => $this->taskRepository->getCountByPriority(),
            'tasks_by_status' => $this->taskRepository->getCountByStatus(),
        ];
    }
}
