<?php

namespace App\Plugins\TaskManager\Console\Commands;

use Illuminate\Console\Command;
use App\Plugins\TaskManager\Models\Task;
use App\Plugins\TaskManager\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Commande de création de données de test pour Task Manager
 *
 * Cette commande crée des données de test réalistes pour le plugin :
 * - Catégories de tâches
 * - Tâches avec différents statuts et priorités
 * - Relations entre utilisateurs et tâches
 */
class TaskManagerSeedCommand extends Command
{
    /**
     * Le nom et la signature de la commande.
     */
    protected $signature = 'task-manager:seed
                            {--count=50 : Nombre de tâches à créer}
                            {--users=5 : Nombre d\'utilisateurs à utiliser}
                            {--categories=8 : Nombre de catégories à créer}
                            {--force : Forcer la création même si des données existent}';

    /**
     * La description de la commande.
     */
    protected $description = 'Crée des données de test pour le plugin Task Manager';

    /**
     * Exécute la commande.
     */
    public function handle(): int
    {
        $this->info('🌱 Création de données de test pour Task Manager...');

        try {
            // Vérifier si des données existent déjà
            if (Task::count() > 0 && !$this->option('force')) {
                $this->warn('⚠️  Des tâches existent déjà dans la base de données.');

                if (!$this->confirm('Voulez-vous continuer et ajouter plus de données ?', false)) {
                    $this->info('❌ Opération annulée.');
                    return self::FAILURE;
                }
            }

            // 1. Créer les catégories
            $categories = $this->createCategories();

            // 2. Récupérer ou créer les utilisateurs
            $users = $this->getUsers();

            // 3. Créer les tâches
            $this->createTasks($categories, $users);

            $this->info('✅ Données de test créées avec succès !');
            $this->displayStats();

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la création des données : ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Crée les catégories de tâches.
     */
    protected function createCategories(): \Illuminate\Database\Eloquent\Collection
    {
        $this->info('📂 Création des catégories...');

        $categoryData = [
            ['name' => 'Développement', 'description' => 'Tâches liées au développement', 'color' => '#3B82F6'],
            ['name' => 'Design', 'description' => 'Tâches de design et UI/UX', 'color' => '#8B5CF6'],
            ['name' => 'Marketing', 'description' => 'Tâches marketing et communication', 'color' => '#10B981'],
            ['name' => 'Support', 'description' => 'Support client et maintenance', 'color' => '#F59E0B'],
            ['name' => 'Administration', 'description' => 'Tâches administratives', 'color' => '#6B7280'],
            ['name' => 'Urgent', 'description' => 'Tâches urgentes à traiter', 'color' => '#EF4444'],
            ['name' => 'Planification', 'description' => 'Planification et stratégie', 'color' => '#06B6D4'],
            ['name' => 'Qualité', 'description' => 'Tests et assurance qualité', 'color' => '#84CC16'],
        ];

        $categories = Category::whereIn('name', array_column($categoryData, 'name'))->get();
        $count = min($this->option('categories'), count($categoryData));

        for ($i = 0; $i < $count; $i++) {
            $category = Category::firstOrCreate(
                ['name' => $categoryData[$i]['name']],
                array_merge($categoryData[$i], [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
            if (!$categories->contains('id', $category->id)) {
                $categories->push($category);
            }
        }

        $this->info("✅ {$categories->count()} catégories créées.");
        return $categories;
    }

    /**
     * Récupère ou crée les utilisateurs.
     */
    protected function getUsers(): \Illuminate\Database\Eloquent\Collection
    {
        $this->info('👥 Récupération des utilisateurs...');

        $users = User::take($this->option('users'))->get();

        if ($users->isEmpty()) {
            $this->warn('⚠️  Aucun utilisateur trouvé. Création d\'utilisateurs de test...');

            // Créer des utilisateurs de test
            $userData = [
                ['name' => 'John Doe', 'email' => 'john@example.com'],
                ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
                ['name' => 'Bob Johnson', 'email' => 'bob@example.com'],
                ['name' => 'Alice Brown', 'email' => 'alice@example.com'],
                ['name' => 'Charlie Wilson', 'email' => 'charlie@example.com'],
            ];

            foreach ($userData as $data) {
                $users->push(User::firstOrCreate(
                    ['email' => $data['email']],
                    array_merge($data, ['password' => bcrypt('password')])
                ));
            }
        }

        $this->info("✅ {$users->count()} utilisateurs disponibles.");
        return $users;
    }

    /**
     * Crée les tâches de test.
     */
    protected function createTasks($categories, $users): void
    {
        $this->info('📝 Création des tâches...');

        $taskTemplates = [
            [
                'titles' => [
                    'Implémenter la fonctionnalité de recherche',
                    'Créer l\'interface utilisateur responsive',
                    'Optimiser les performances de la base de données',
                    'Corriger les bugs de validation',
                    'Ajouter les tests unitaires',
                ],
                'descriptions' => [
                    'Développer une fonctionnalité de recherche avancée avec filtres multiples.',
                    'Créer une interface utilisateur responsive pour tous les appareils.',
                    'Optimiser les requêtes de base de données pour améliorer les performances.',
                    'Corriger les problèmes de validation des formulaires.',
                    'Écrire des tests unitaires complets pour les nouvelles fonctionnalités.',
                ],
                'status' => 'in_progress',
                'priority' => 'high',
            ],
            [
                'titles' => [
                    'Réviser le design du logo',
                    'Créer les maquettes pour la page d\'accueil',
                    'Optimiser l\'expérience utilisateur',
                    'Créer les icônes personnalisées',
                    'Finaliser la charte graphique',
                ],
                'descriptions' => [
                    'Réviser et améliorer le design du logo de l\'entreprise.',
                    'Créer des maquettes détaillées pour la nouvelle page d\'accueil.',
                    'Analyser et optimiser l\'expérience utilisateur globale.',
                    'Créer un ensemble d\'icônes personnalisées pour l\'application.',
                    'Finaliser la charte graphique complète de l\'entreprise.',
                ],
                'status' => 'pending',
                'priority' => 'medium',
            ],
            [
                'titles' => [
                    'Lancer la campagne email',
                    'Créer le contenu pour les réseaux sociaux',
                    'Analyser les métriques de conversion',
                    'Optimiser le SEO du site web',
                    'Planifier la stratégie marketing Q4',
                ],
                'descriptions' => [
                    'Lancer une campagne email marketing ciblée.',
                    'Créer du contenu engageant pour les réseaux sociaux.',
                    'Analyser et optimiser les métriques de conversion.',
                    'Améliorer le référencement naturel du site web.',
                    'Planifier la stratégie marketing pour le 4ème trimestre.',
                ],
                'status' => 'completed',
                'priority' => 'low',
            ],
        ];

        $count = $this->option('count');
        $bar = $this->output->createProgressBar($count);

        for ($i = 0; $i < $count; $i++) {
            $template = $taskTemplates[array_rand($taskTemplates)];
            $titleIndex = array_rand($template['titles']);
            $descriptionIndex = array_rand($template['descriptions']);

            $task = Task::create([
                'title' => $template['titles'][$titleIndex],
                'description' => $template['descriptions'][$descriptionIndex],
                'status' => $template['status'],
                'priority' => $template['priority'],
                'progress' => rand(0, 100),
                'due_date' => now()->addDays(rand(-30, 60)),
                'category_id' => $categories->random()->id,
                'created_by' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'is_public' => rand(0, 1),
                'estimated_hours' => rand(1, 40),
                'actual_hours' => rand(0, 35),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ {$count} tâches créées avec succès.");
    }

    /**
     * Affiche les statistiques des données créées.
     */
    protected function displayStats(): void
    {
        $this->newLine();
        $this->info('📊 Statistiques des données créées :');

        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Total des tâches', Task::count()],
                ['Tâches en attente', Task::where('status', 'pending')->count()],
                ['Tâches en cours', Task::where('status', 'in_progress')->count()],
                ['Tâches terminées', Task::where('status', 'completed')->count()],
                ['Tâches en retard', Task::overdue()->count()],
                ['Catégories', Category::count()],
                ['Utilisateurs impliqués', User::whereHas('assignedTasks')->count()],
            ]
        );
    }
}
