<?php

namespace App\Plugins\TaskManager\Console\Commands;

use Illuminate\Console\Command;
use App\Plugins\TaskManager\Models\Task;
use App\Plugins\TaskManager\Models\Category;
use App\Plugins\TaskManager\Services\ExportService;
use Illuminate\Support\Facades\Storage;

/**
 * Commande d'export des données Task Manager
 */
class TaskManagerExportCommand extends Command
{
    protected $signature = 'task-manager:export
                            {format=xlsx : Format d\'export (xlsx, csv, json)}
                            {--filters= : Filtres JSON pour les tâches}
                            {--output= : Chemin de sortie personnalisé}';

    protected $description = 'Exporte les données du plugin Task Manager';

    public function handle(ExportService $exportService): int
    {
        $format = $this->argument('format');
        $filters = $this->option('filters') ? json_decode($this->option('filters'), true) : [];
        $output = $this->option('output');

        try {
            $this->info("📊 Export des données Task Manager en format {$format}...");

            $result = $exportService->exportTasks($format, $filters);

            if ($output) {
                Storage::put($output, $result->getContent());
                $this->info("✅ Export sauvegardé dans : {$output}");
            } else {
                $this->info("✅ Export terminé avec succès !");
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de l'export : " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
