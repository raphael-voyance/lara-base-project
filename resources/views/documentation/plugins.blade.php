<x-guest-layout>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700 transition duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h1 class="ml-4 text-2xl font-bold text-gray-900">Documentation</h1>
                </div>
                <nav class="flex space-x-8">
                    <a href="{{ route('doc.index') }}" class="text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 pb-2 px-1 text-sm font-medium transition duration-300">
                        Accueil
                    </a>
                    <a href="{{ route('doc.plugins') }}" class="text-indigo-600 border-b-2 border-indigo-600 pb-2 px-1 text-sm font-medium">
                        Architecture modulaire
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Introduction -->
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Architecture modulaire</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Découvrez comment créer et intégrer vos propres plugins dans l'architecture modulaire de ce projet Laravel.
            </p>
        </div>

        <!-- Table des matières -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-12">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6">Table des matières</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <ul class="space-y-3">
                    <li><a href="#concept" class="text-emerald-600 hover:text-emerald-700 transition duration-300">1. Concept de l'architecture modulaire</a></li>
                    <li><a href="#structure" class="text-emerald-600 hover:text-emerald-700 transition duration-300">2. Structure d'un plugin</a></li>
                    <li><a href="#installation" class="text-emerald-600 hover:text-emerald-700 transition duration-300">3. Installation et configuration</a></li>
                </ul>
                <ul class="space-y-3">
                    <li><a href="#creation" class="text-emerald-600 hover:text-emerald-700 transition duration-300">4. Création d'un nouveau plugin</a></li>
                    <li><a href="#exemple" class="text-emerald-600 hover:text-emerald-700 transition duration-300">5. Exemple pratique</a></li>
                    <li><a href="#bonnes-pratiques" class="text-emerald-600 hover:text-emerald-700 transition duration-300">6. Bonnes pratiques</a></li>
                </ul>
            </div>
        </div>

        <!-- Contenu détaillé -->
        <div class="space-y-12">
            <!-- Concept -->
            <section id="concept" class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">1. Concept de l'architecture modulaire</h3>
                <div class="prose prose-lg max-w-none">
                    <p class="text-gray-600 mb-6">
                        L'architecture modulaire de ce projet permet de créer des plugins autonomes et réutilisables qui s'intègrent parfaitement dans l'écosystème Laravel. Chaque plugin peut contenir ses propres modèles, contrôleurs, vues, migrations et services.
                    </p>
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Avantages de l'architecture modulaire :</h4>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                <span>Isolation du code et des fonctionnalités</span>
                            </li>
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                <span>Réutilisabilité entre projets</span>
                            </li>
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                <span>Maintenance simplifiée</span>
                            </li>
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                <span>Évolutivité de l'application</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Structure -->
            <section id="structure" class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">2. Structure d'un plugin</h3>
                <div class="prose prose-lg max-w-none">
                    <p class="text-gray-600 mb-6">
                        Chaque plugin suit une structure standardisée qui facilite son intégration et sa maintenance.
                    </p>
                    <div class="bg-gray-900 rounded-lg p-6 mb-6 overflow-x-auto">
                        <pre class="text-green-400 text-sm"><code>app/
├── Plugins/
│   └── MonPlugin/
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
    └── PluginServiceProvider.php</code></pre>
                    </div>
                </div>
            </section>

            <!-- Installation -->
            <section id="installation" class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">3. Installation et configuration</h3>
                <div class="prose prose-lg max-w-none">
                    <h4 class="text-xl font-semibold text-gray-900 mb-4">Configuration globale</h4>
                    <p class="text-gray-600 mb-6">
                        Le système de plugins est configuré via le fichier <code class="bg-gray-100 px-2 py-1 rounded">config/plugins.php</code> :
                    </p>
                    <div class="bg-gray-900 rounded-lg p-6 mb-6 overflow-x-auto">
                        <pre class="text-green-400 text-sm"><code>&lt;?php
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
];</code></pre>
                    </div>
                </div>
            </section>

            <!-- Création -->
            <section id="creation" class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">4. Création d'un nouveau plugin</h3>
                <div class="prose prose-lg max-w-none">
                    <h4 class="text-xl font-semibold text-gray-900 mb-4">Étapes de création</h4>
                    <div class="space-y-4">
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <h5 class="font-semibold text-blue-900 mb-2">Étape 1 : Créer la structure</h5>
                            <p class="text-blue-800">Créez le dossier de votre plugin dans <code class="bg-blue-100 px-2 py-1 rounded">app/Plugins/VotrePlugin/</code></p>
                        </div>
                        <div class="bg-green-50 border-l-4 border-green-400 p-4">
                            <h5 class="font-semibold text-green-900 mb-2">Étape 2 : Service Provider</h5>
                            <p class="text-green-800">Créez votre <code class="bg-green-100 px-2 py-1 rounded">PluginServiceProvider</code> pour enregistrer les services</p>
                        </div>
                        <div class="bg-purple-50 border-l-4 border-purple-400 p-4">
                            <h5 class="font-semibold text-purple-900 mb-2">Étape 3 : Configuration</h5>
                            <p class="text-purple-800">Ajoutez votre plugin dans <code class="bg-purple-100 px-2 py-1 rounded">config/plugins.php</code></p>
                        </div>
                        <div class="bg-orange-50 border-l-4 border-orange-400 p-4">
                            <h5 class="font-semibold text-orange-900 mb-2">Étape 4 : Développement</h5>
                            <p class="text-orange-800">Développez vos modèles, contrôleurs, vues et fonctionnalités</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Exemple -->
            <section id="exemple" class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">5. Exemple pratique</h3>
                <div class="prose prose-lg max-w-none">
                    <p class="text-gray-600 mb-6">
                        Voici un exemple de service provider pour un plugin :
                    </p>
                    <div class="bg-gray-900 rounded-lg p-6 mb-6 overflow-x-auto">
                        <pre class="text-green-400 text-sm"><code>&lt;?php
namespace App\Plugins\MonPlugin\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class PluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Enregistrer les services
        $this->app->singleton('mon-plugin', function ($app) {
            return new MonPluginService();
        });
    }

    public function boot(): void
    {
        // Charger les routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Charger les vues
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mon-plugin');

        // Charger les migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}</code></pre>
                    </div>
                </div>
            </section>

            <!-- Bonnes pratiques -->
            <section id="bonnes-pratiques" class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">6. Bonnes pratiques</h3>
                <div class="prose prose-lg max-w-none">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-emerald-50 rounded-lg p-6">
                            <h4 class="font-semibold text-emerald-900 mb-3">✅ À faire</h4>
                            <ul class="space-y-2 text-emerald-800">
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    <span>Suivre les conventions PSR-4</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    <span>Utiliser les traits Laravel appropriés</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    <span>Documenter vos méthodes</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    <span>Ajouter des tests</span>
                                </li>
                            </ul>
                        </div>
                        <div class="bg-red-50 rounded-lg p-6">
                            <h4 class="font-semibold text-red-900 mb-3">❌ À éviter</h4>
                            <ul class="space-y-2 text-red-800">
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    <span>Coupler les plugins entre eux</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    <span>Modifier le code core</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    <span>Oublier la gestion des erreurs</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    <span>Négliger la sécurité</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Call to action -->
        <div class="text-center mt-16">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-8 text-white">
                <h2 class="text-2xl font-bold mb-4">Prêt à créer votre premier plugin ?</h2>
                <p class="text-emerald-100 mb-6 max-w-2xl mx-auto">
                    Commencez par explorer l'exemple du Task Manager Plugin pour comprendre l'architecture en action.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-white text-emerald-600 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                        Retour à l'accueil
                    </a>
                    <a href="https://github.com/ton-repo" target="_blank" class="inline-block px-6 py-3 bg-emerald-500 text-white rounded-lg font-semibold hover:bg-emerald-600 transition duration-300">
                        Voir le code source
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</x-guest-layout>
