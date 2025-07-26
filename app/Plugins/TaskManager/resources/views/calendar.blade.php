@extends('layouts.app')

@section('title', 'Calendrier - Task Manager')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Calendrier des tâches</h1>
            <p class="mt-2 text-gray-600">Vue calendrier de vos tâches et échéances</p>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="in_progress">En cours</option>
                        <option value="completed">Terminée</option>
                        <option value="cancelled">Annulée</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priorité</label>
                    <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Toutes les priorités</option>
                        <option value="low">Basse</option>
                        <option value="medium">Moyenne</option>
                        <option value="high">Haute</option>
                        <option value="urgent">Urgente</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                    <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Toutes les catégories</option>
                        @if(isset($categories))
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Filtrer
                    </button>
                </div>
            </div>
        </div>

        <!-- Calendrier -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Calendrier</h3>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                            Aujourd'hui
                        </button>
                        <button class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                            ←
                        </button>
                        <button class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                            →
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <!-- Placeholder pour le calendrier -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Calendrier en cours de développement</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Cette fonctionnalité sera bientôt disponible avec une vue calendrier interactive.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('task-manager.tasks.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Voir la liste des tâches
                        </a>
                    </div>
                </div>

                <!-- Liste des tâches avec dates d'échéance -->
                @if(isset($tasks) && $tasks->count() > 0)
                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Tâches avec échéances</h4>
                        <div class="space-y-3">
                            @foreach($tasks as $task)
                                @if($task->due_date)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg
                                        @if($task->due_date->isPast() && $task->status !== 'completed') bg-red-50 border-red-200 @endif">
                                        <div class="flex-1">
                                            <h5 class="text-sm font-medium text-gray-900">{{ $task->title }}</h5>
                                            <p class="text-sm text-gray-500">
                                                Échéance : {{ $task->due_date->format('d/m/Y H:i') }}
                                                @if($task->due_date->isPast() && $task->status !== 'completed')
                                                    <span class="text-red-600 font-medium"> (En retard)</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($task->status === 'completed') bg-green-100 text-green-800
                                                @elseif($task->status === 'in_progress') bg-yellow-100 text-yellow-800
                                                @elseif($task->status === 'cancelled') bg-gray-100 text-gray-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ ucfirst($task->status) }}
                                            </span>
                                            <a href="{{ route('task-manager.tasks.show', $task) }}"
                                               class="text-blue-600 hover:text-blue-800 text-sm">
                                                Voir
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">Aucune tâche avec échéance trouvée.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
