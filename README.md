<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Logo Laravel"></a></p>

# Base Project Laravel

Ce projet est un squelette d’application web basé sur le framework **Laravel 12** (PHP ^8.2), enrichi de nombreux packages pour accélérer le développement d’applications modernes, multilingues et robustes.

## Fonctionnalités principales

- Authentification complète (Livewire, gestion des emails, vérification, réinitialisation, etc.)
- Gestion des rôles et permissions (spatie/laravel-permission)
- Upload et gestion de médias (spatie/laravel-medialibrary)
- SEO (artesaos/seotools)
- Génération de PDF (barryvdh/laravel-dompdf)
- Notifications toast (devrabiul/laravel-toaster-magic)
- Consentement cookies, honeypot anti-spam, backup, sitemap, recherche, etc. (Spatie)
- Actions Laravel (lorisleiva/laravel-actions)
- Prise en charge du français (laravel-lang/common)
- Frontend moderne avec **Vite** et **Tailwind CSS 4**
- Composants Livewire 3
- Outils de développement : Debugbar, Pest, Pint, Sail, etc.

## Stack technique

### Backend (PHP)

- **Laravel 12**
- PHP ^8.2
- Livewire ^3.6
- spatie/laravel-permission, spatie/laravel-medialibrary, spatie/laravel-backup, spatie/laravel-cookie-consent, spatie/laravel-honeypot, spatie/laravel-searchable, spatie/laravel-sitemap
- barryvdh/laravel-dompdf
- artesaos/seotools
- lorisleiva/laravel-actions
- devrabiul/laravel-toaster-magic
- laravel-lang/common

### Frontend (JS)

- **Vite** ^6.2.4
- **Tailwind CSS** ^4.0.0 (+ plugins forms, typography)
- laravel-vite-plugin
- @tailwindcss/vite
- concurrently (pour le dev multi-processus)

### Outils de développement

- **Pest** (tests)
- **Laravel Debugbar**
- **Laravel Pint** (formatage)
- **Laravel Sail** (environnement Docker)
- **Wulfheart Laravel Actions IDE Helper**
- **Faker** (génération de données de test)
- **Mockery** (mocks pour tests)

## Installation

1. **Cloner le dépôt**
2. Installer les dépendances PHP :
   ```bash
   composer install
   ```
3. Installer les dépendances JS :
   ```bash
   npm install
   ```
4. Copier le fichier d’environnement :
   ```bash
   cp .env.example .env
   ```
5. Générer la clé d’application :
   ```bash
   php artisan key:generate
   ```
6. Lancer les migrations :
   ```bash
   php artisan migrate
   ```
7. Lancer le serveur de développement :
   ```bash
   npm run dev
   # ou
   composer dev
   ```

## Scripts utiles

- `npm run dev` : Lance Vite en mode développement
- `npm run build` : Build de production
- `composer dev` : Lance serveur PHP, queue, logs et Vite en parallèle (voir scripts composer)
- `composer test` : Lance les tests

## Structure du projet

- `app/` : Code applicatif Laravel (contrôleurs, modèles, Livewire, etc.)
- `resources/` : Vues Blade, assets CSS/JS
- `routes/` : Fichiers de routes
- `config/` : Fichiers de configuration
- `database/` : Migrations, seeders, factories
- `public/` : Fichiers accessibles publiquement

## Localisation

Le projet est prêt pour le français (`lang/fr/` et `fr.json`).

## Contribution

Merci de consulter la documentation Laravel et les conventions du projet avant toute contribution.

## Licence

Ce projet est sous licence MIT.

# Task Manager Plugin pour Laravel

## 📋 Description
Le **Task Manager Plugin** est un plugin Laravel complet et moderne pour la gestion de tâches, conçu en suivant les meilleures pratiques du framework et utilisant la stack TALL (Tailwind CSS, Alpine.js, Laravel, Livewire).

## 🚀 Prérequis techniques
- **PHP** : 8.1 ou supérieur
- **Laravel** : 10.x ou supérieur
- **Composer** : 2.0 ou supérieur
- **Node.js** : 16.x ou supérieur (pour les assets)
- **MySQL** : 5.7 ou supérieur (ou PostgreSQL, SQLite)

## 📦 Installation complète

### 1. Structure du projet
Le plugin est organisé dans une architecture modulaire :

```
app/
├── Plugins/
│   └── TaskManager/
│       ├── config/
│       │   └── config.php
│       ├── database/
│       │   └── migrations/
│       ├── Http/
│       │   ├── Controllers/
│       │   ├── Livewire/
│       │   ├── Requests/
│       │   └── Middleware/
│       ├── Models/
│       ├── Providers/
│       ├── Services/
│       ├── Repositories/
│       ├── Policies/
│       ├── Events/
│       ├── Listeners/
│       ├── Observers/
│       ├── Console/
│       │   └── Commands/
│       ├── resources/
│       │   ├── views/
│       │   └── assets/
│       └── routes/
└── Providers/
    └── PluginServiceProvider.php
```

### 2. Configuration globale des plugins

Créez le fichier `config/plugins.php` :

```php
<?php
return [
    'plugins_path' => app_path('Plugins'),
    'namespace' => 'App\\Plugins',
    'enabled' => [
        'TaskManager' => true,
        'Blog' => false,
        'Ecommerce' => false,
    ],
    'defaults' => [
        'auto_discover' => true,
        'auto_register' => true,
        'publish_assets' => true,
        'load_migrations' => true,
        'load_routes' => true,
        'load_views' => true,
        'load_translations' => true,
    ],
];
```

### 3. Service Provider principal

Créez `app/Providers/PluginServiceProvider.php` :

```php
<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class PluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/plugins.php', 'plugins');
    }

    public function boot(): void
    {
        $this->loadPlugins();
    }

    protected function loadPlugins(): void
    {
        $pluginsConfig = config('plugins', []);
        $enabledPlugins = $pluginsConfig['enabled'] ?? [];

        foreach ($enabledPlugins as $pluginName => $isEnabled) {
            if ($isEnabled) {
                $this->loadPlugin($pluginName);
            }
        }
    }

    protected function loadPlugin(string $pluginName): void
    {
        $pluginsPath = config('plugins.plugins_path', app_path('Plugins'));
        $pluginPath = $pluginsPath . '/' . $pluginName;
        $pluginNamespace = config('plugins.namespace', 'App\\Plugins');

        if (!is_dir($pluginPath)) {
            return;
        }

        // Charger le service provider du plugin
        $serviceProviderClass = $pluginNamespace . '\\' . $pluginName . '\\Providers\\PluginServiceProvider';
        
        if (class_exists($serviceProviderClass)) {
            $this->app->register($serviceProviderClass);
        }

        // Charger les routes du plugin
        $routesPath = $pluginPath . '/routes/web.php';
        if (file_exists($routesPath)) {
            Route::middleware('web')->group($routesPath);
        }

        // Charger les vues du plugin
        $viewsPath = $pluginPath . '/resources/views';
        if (is_dir($viewsPath)) {
            View::addNamespace(strtolower($pluginName), $viewsPath);
        }
    }
}
```

### 4. Enregistrement du service provider

Ajoutez le service provider dans `bootstrap/providers.php` :

```php
<?php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\PluginServiceProvider::class, // ← Ajoutez cette ligne
];
```

### 5. Installation du plugin

Exécutez les commandes suivantes :

```bash
# Installation complète du plugin
php artisan task-manager:install

# Création des données de test
php artisan task-manager:seed

# Démarrer le serveur de développement
php artisan serve
```

## 🌐 URLs d'accès

Une fois installé, le plugin est accessible via les URLs suivantes :

| Fonctionnalité | URL |
|----------------|-----|
| **Tableau de bord** | `http://localhost:8000/tasks/` |
| **Liste des tâches** | `http://localhost:8000/tasks/tasks` |
| **Créer une tâche** | `http://localhost:8000/tasks/tasks/create` |
| **Calendrier** | `http://localhost:8000/tasks/calendar` |
| **Rapports** | `http://localhost:8000/tasks/reports` |
| **Catégories** | `http://localhost:8000/tasks/categories` |
| **Paramètres** | `http://localhost:8000/tasks/settings` |

## 🛠️ Commandes Artisan

### Installation et configuration
```bash
# Installation complète du plugin
php artisan task-manager:install

# Installation avec dépendances Composer
php artisan task-manager:install --dependencies

# Installation forcée (même si déjà installé)
php artisan task-manager:install --force

# Installation avec données de test
php artisan task-manager:install --seed

# Publication des assets
php artisan task-manager:install --publish

# Installation complète avec toutes les options
php artisan task-manager:install --dependencies --seed --publish
```

### Gestion des données
```bash
# Créer des données de test
php artisan task-manager:seed

# Créer 100 tâches de test
php artisan task-manager:seed --count=100

# Forcer la création même si des données existent
php artisan task-manager:seed --force
```

### Gestion des dépendances
```bash
# Vérifier l'état des dépendances
php artisan task-manager:dependencies check

# Lister toutes les dépendances
php artisan task-manager:dependencies list

# Installer les dépendances manquantes
php artisan task-manager:dependencies install

# Installer un package spécifique
php artisan task-manager:dependencies install --package=maatwebsite/excel

# Mettre à jour les dépendances
php artisan task-manager:dependencies update

# Installation forcée sans confirmation
php artisan task-manager:dependencies install --force
```

### Gestion des permissions
```bash
# Configurer les permissions de base
php artisan task-manager:permissions setup

# Vérifier l'état des permissions
php artisan task-manager:permissions check

# Assigner le rôle à un utilisateur (par ID)
php artisan task-manager:permissions assign --user=1

# Assigner le rôle à un utilisateur (par email)
php artisan task-manager:permissions assign --user=test@example.com

# Lister toutes les permissions et rôles
php artisan task-manager:permissions list
```

### Export et rapports
```bash
# Exporter les tâches en Excel
php artisan task-manager:export xlsx

# Exporter en CSV
php artisan task-manager:export csv

# Exporter en JSON
php artisan task-manager:export json

# Exporter avec filtres
php artisan task-manager:export xlsx --filters='{"status":"completed"}'
```

## 📊 Fonctionnalités

### ✅ Fonctionnalités principales
- **Gestion complète des tâches** (CRUD)
- **Catégorisation** avec hiérarchie
- **Système de priorités** (Basse, Moyenne, Haute, Urgente)
- **Statuts de tâches** (En attente, En cours, Terminée, Annulée)
- **Assignation d'utilisateurs**
- **Dates d'échéance** avec alertes de retard
- **Progression** en pourcentage
- **Commentaires** et fichiers attachés
- **Dépendances** entre tâches
- **Sous-tâches** et tâches parentes

### 🎨 Interface utilisateur
- **Design moderne** avec Tailwind CSS
- **Interface responsive** pour mobile et desktop
- **Composants Livewire** pour l'interactivité
- **Filtres avancés** et recherche
- **Tri et pagination**
- **Actions en lot** sur les tâches
- **Vue calendrier** et tableau de bord
- **Rapports** et statistiques

### 🔧 Fonctionnalités avancées
- **Système de permissions** granulaire
- **Notifications** par email et base de données
- **Export** en multiple formats (Excel, CSV, PDF, JSON)
- **API REST** pour intégrations
- **Webhooks** pour événements
- **Suivi du temps** et rapports
- **Historique** des activités
- **Étiquettes** et tags
- **Fichiers attachés** avec gestion des types
- **Commentaires** internes et publics

## 🏗️ Architecture technique

### Modèles Eloquent
- `Task` : Gestion des tâches principales
- `Category` : Organisation des catégories
- `TaskComment` : Commentaires sur les tâches
- `TaskAttachment` : Fichiers attachés
- `TaskTag` : Système d'étiquettes
- `TaskActivity` : Historique des activités
- `TaskTimeEntry` : Suivi du temps

### Services
- `TaskService` : Logique métier des tâches
- `CategoryService` : Gestion des catégories
- `NotificationService` : Envoi de notifications
- `ExportService` : Export des données
- `TaskManagerService` : Facade principal

### Repositories
- `TaskRepository` : Accès aux données des tâches
- `CategoryRepository` : Accès aux données des catégories

### Contrôleurs
- `TaskController` : Gestion des requêtes HTTP pour les tâches
- Actions AJAX pour les changements de statut/priorité
- Export et rapports

### Composants Livewire
- `TaskList` : Liste interactive des tâches
- Filtres en temps réel
- Actions en lot
- Pagination dynamique

## 📦 Gestion des dépendances

### Dépendances requises
Le plugin Task Manager nécessite plusieurs packages Composer pour fonctionner correctement :

#### Dépendances principales (requises)
- **livewire/livewire** : Composants interactifs pour Laravel
- **spatie/laravel-permission** : Gestion des permissions et rôles

#### Dépendances optionnelles
- **spatie/laravel-activitylog** : Journalisation des activités
- **spatie/laravel-medialibrary** : Gestion des fichiers médias
- **maatwebsite/excel** : Export Excel des données
- **barryvdh/laravel-dompdf** : Génération de PDF
- **spatie/laravel-notification-log** : Journalisation des notifications
- **laravel/sanctum** : Authentification API
- **predis/predis** : Client Redis pour le cache
- **fakerphp/faker** : Génération de données de test

### Configuration des dépendances
Les dépendances sont configurées dans `app/Plugins/TaskManager/config/dependencies.php` :

```php
'required' => [
    'livewire/livewire' => [
        'version' => '^3.0',
        'description' => 'Composants interactifs pour Laravel',
        'required' => true,
    ],
    // ... autres dépendances
],
```

### Scripts post-installation
Certains packages nécessitent des étapes supplémentaires après installation :

```php
'post_install_scripts' => [
    'spatie/laravel-permission' => [
        'php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"',
        'php artisan migrate',
    ],
    // ... autres scripts
],
```

## 🔐 Sécurité et permissions

### Système de permissions
```php
// Permissions disponibles
'view_tasks' => 'Voir les tâches',
'create_tasks' => 'Créer des tâches',
'edit_tasks' => 'Modifier les tâches',
'delete_tasks' => 'Supprimer les tâches',
'assign_tasks' => 'Assigner des tâches',
'export_tasks' => 'Exporter les tâches',
'view_reports' => 'Voir les rapports',
'manage_categories' => 'Gérer les catégories',
```

### Middleware de sécurité
- `task-manager.auth` : Authentification requise
- `task-manager.permission` : Vérification des permissions
- `task-manager.rate-limit` : Limitation de débit

## 🎯 Utilisation

### Créer une tâche
```php
use App\Plugins\TaskManager\Services\TaskService;

$taskService = app(TaskService::class);

$task = $taskService->createTask([
    'title' => 'Nouvelle tâche',
    'description' => 'Description de la tâche',
    'priority' => 'high',
    'due_date' => now()->addDays(7),
    'category_id' => 1,
    'assigned_to' => 2,
]);
```

### Lister les tâches
```php
// Toutes les tâches
$tasks = $taskService->getTasks();

// Avec filtres
$tasks = $taskService->getTasks([
    'status' => 'pending',
    'priority' => 'high',
    'assigned_to' => auth()->id(),
]);
```

### Utiliser le facade
```php
use App\Plugins\TaskManager\Services\TaskManagerService;

// Statistiques
$stats = app('task-manager')->getStats();

// Tâches en retard
$overdue = app('task-manager')->getOverdueTasks();

// Libellés
$statusLabel = app('task-manager')->getStatusLabel('completed');
```

## 🎨 Personnalisation

### Configuration
Modifiez `app/Plugins/TaskManager/config/config.php` pour :
- Changer les statuts et priorités
- Personnaliser les couleurs
- Configurer les notifications
- Définir les permissions

### Thème
Les styles sont dans `app/Plugins/TaskManager/resources/assets/css/task-manager.css`
Personnalisez les variables CSS pour adapter le design.

### Traductions
Ajoutez vos traductions dans `app/Plugins/TaskManager/lang/`

## 🧪 Tests

### Tests unitaires
```bash
# Tests du plugin Task Manager
php artisan test --filter=TaskManager
```

### Tests de fonctionnalités
```bash
# Tests des commandes
php artisan test --filter=TaskManagerCommand

# Tests des services
php artisan test --filter=TaskService
```

## 🚀 Déploiement

### Production
```bash
# Optimiser pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Publier les assets
php artisan vendor:publish --tag=task-manager-assets
```

### Variables d'environnement
```env
# Configuration du plugin
TASK_MANAGER_NAME="Gestionnaire de Tâches"
TASK_MANAGER_ROUTE_PREFIX="tasks"

# Notifications
TASK_MANAGER_NOTIFICATIONS_ENABLED=true
TASK_MANAGER_EMAIL_NOTIFICATIONS=true
```

## 🔧 Dépannage

### Problèmes courants

**Erreur : "Table doesn't exist"**
```bash
php artisan migrate:fresh
php artisan task-manager:install
```

**Erreur : "Class not found"**
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

**Erreur : "Route not found"**
```bash
php artisan route:clear
php artisan route:cache
```

### Logs
Les logs du plugin sont dans `storage/logs/laravel.log`

## 🤝 Contribution

### Structure pour ajouter un nouveau plugin
1. Créez le dossier `app/Plugins/VotrePlugin/`
2. Suivez la même structure que TaskManager
3. Ajoutez votre plugin dans `config/plugins.php`
4. Créez votre `PluginServiceProvider`

### Bonnes pratiques
- Suivez les conventions PSR-4
- Utilisez les traits Laravel appropriés
- Documentez vos méthodes
- Ajoutez des tests
- Respectez la séparation des responsabilités

## 📄 Licence

Ce plugin est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 🙏 Remerciements

- **Laravel** pour le framework exceptionnel
- **Tailwind CSS** pour les styles
- **Livewire** pour l'interactivité
- **Alpine.js** pour les interactions côté client

## 📞 Support

Pour toute question ou problème :
- Ouvrez une issue sur GitHub
- Consultez la documentation
- Contactez l'équipe de développement

---

**Task Manager Plugin** - Une solution complète et moderne pour la gestion de tâches dans Laravel ! 🚀
