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
                    <a href="{{ route('doc.index') }}" class="text-indigo-600 border-b-2 border-indigo-600 pb-2 px-1 text-sm font-medium">
                        Accueil
                    </a>
                    <a href="{{ route('doc.plugins') }}" class="text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 pb-2 px-1 text-sm font-medium transition duration-300">
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
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Documentation du projet</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Découvrez comment utiliser et étendre ce projet Laravel moderne avec ses fonctionnalités avancées et son architecture modulaire.
            </p>
        </div>

        <!-- Sections de documentation -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            <!-- Architecture modulaire -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Architecture modulaire</h3>
                <p class="text-gray-600 mb-6">
                    Apprenez à créer et intégrer vos propres plugins dans l'architecture modulaire du projet.
                </p>
                <a href="{{ route('doc.plugins') }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium transition duration-300">
                    En savoir plus
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <!-- Authentification -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Authentification</h3>
                <p class="text-gray-600 mb-6">
                    Système d'authentification complet avec Livewire, vérification d'email et réinitialisation de mot de passe.
                </p>
                <span class="text-gray-400 text-sm">Bientôt disponible</span>
            </div>

            <!-- Permissions -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Gestion des permissions</h3>
                <p class="text-gray-600 mb-6">
                    Système de rôles et permissions granulaire basé sur Spatie Laravel Permission.
                </p>
                <span class="text-gray-400 text-sm">Bientôt disponible</span>
            </div>

            <!-- Médias -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Gestion des médias</h3>
                <p class="text-gray-600 mb-6">
                    Upload et gestion avancée des fichiers avec Spatie Media Library.
                </p>
                <span class="text-gray-400 text-sm">Bientôt disponible</span>
            </div>

            <!-- SEO -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Optimisation SEO</h3>
                <p class="text-gray-600 mb-6">
                    Outils SEO, sitemap automatique et métadonnées avancées pour votre application.
                </p>
                <span class="text-gray-400 text-sm">Bientôt disponible</span>
            </div>

            <!-- Notifications -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-5 5v-5zM4.19 4.19A2 2 0 004 6v10a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-1.81 1.19z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Notifications</h3>
                <p class="text-gray-600 mb-6">
                    Système de notifications toast élégant et interactif pour une meilleure UX.
                </p>
                <span class="text-gray-400 text-sm">Bientôt disponible</span>
            </div>
        </div>

        <!-- Ressources utilisées -->
        <div class="max-w-4xl mx-auto mt-16 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Ressources utilisées</h2>
            <ul class="list-disc list-inside text-gray-700 space-y-2">
                <li>
                    <a href="https://laravel.com/docs" target="_blank" class="text-indigo-600 hover:underline">Laravel</a> – Framework PHP principal du projet
                </li>
                <li>
                    <a href="https://laravel-livewire.com/docs" target="_blank" class="text-indigo-600 hover:underline">Livewire</a> – Composants dynamiques pour Laravel
                </li>
                <li>
                    <a href="https://spatie.be/docs/laravel-permission" target="_blank" class="text-indigo-600 hover:underline">Spatie Laravel Permission</a> – Gestion des rôles et permissions
                </li>
                <li>
                    <a href="https://spatie.be/docs/laravel-medialibrary" target="_blank" class="text-indigo-600 hover:underline">Spatie Media Library</a> – Gestion avancée des fichiers et médias
                </li>
                <li>
                    <a href="https://tailwindcss.com/docs" target="_blank" class="text-indigo-600 hover:underline">Tailwind CSS</a> – Framework CSS utilitaire pour le design
                </li>
                <li>
                    <a href="https://filamentphp.com/docs" target="_blank" class="text-indigo-600 hover:underline">Filament</a> – Panneau d'administration moderne pour Laravel
                </li>
                <li>
                    <a href="https://github.com/devrabiul/laravel-toaster-magic" target="_blank" class="text-indigo-600 hover:underline">Laravel Toaster Magic</a> – Notifications toast pour Laravel
                </li>
            </ul>
        </div>
        <!-- Fin ressources utilisées -->

        <!-- Call to action -->
        <div class="text-center">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white">
                <h2 class="text-2xl font-bold mb-4">Besoin d'aide ?</h2>
                <p class="text-indigo-100 mb-6 max-w-2xl mx-auto">
                    Si vous ne trouvez pas ce que vous cherchez dans cette documentation, n'hésitez pas à consulter les ressources externes ou à nous contacter.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="https://laravel.com/docs" target="_blank" class="inline-block px-6 py-3 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                        Documentation Laravel
                    </a>
                    <a href="mailto:contact@tonprojet.fr" class="inline-block px-6 py-3 bg-emerald-500 text-white rounded-lg font-semibold hover:bg-emerald-600 transition duration-300">
                        Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</x-guest-layout>
