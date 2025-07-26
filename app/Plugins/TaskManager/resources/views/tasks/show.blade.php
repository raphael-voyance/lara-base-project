@extends('layouts.app')

@section('title', $task->title . ' - Task Manager')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $task->title }}</h1>
                    <p class="mt-2 text-gray-600">Détails de la tâche</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('task-manager.tasks.edit', $task) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Modifier
                    </a>
                    <a href="{{ route('task-manager.tasks.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations principales -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Description</h3>
                    </div>
                    <div class="p-6">
                        @if($task->description)
                            <div class="prose max-w-none">
                                {!! nl2br(e($task->description)) !!}
                            </div>
                        @else
                            <p class="text-gray-500 italic">Aucune description fournie.</p>
                        @endif
                    </div>
                </div>

                <!-- Progression -->
                @if($task->progress > 0)
                    <div class="mt-6 bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Progression</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">{{ $task->progress }}%</span>
                                <span class="text-sm text-gray-500">Terminé</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $task->progress }}%"></div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Commentaires -->
                <div class="mt-6 bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Commentaires</h3>
                    </div>
                    <div class="p-6">
                        @if($task->comments && $task->comments->count() > 0)
                            <div class="space-y-4">
                                @foreach($task->comments as $comment)
                                    <div class="border-l-4 border-blue-500 pl-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-900">{{ $comment->user->name ?? 'Utilisateur' }}</span>
                                            <span class="text-sm text-gray-500">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-700">{{ $comment->content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">Aucun commentaire pour le moment.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Statut et priorité -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informations</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Statut -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($task->status === 'completed') bg-green-100 text-green-800
                                @elseif($task->status === 'in_progress') bg-yellow-100 text-yellow-800
                                @elseif($task->status === 'cancelled') bg-gray-100 text-gray-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>

                        <!-- Priorité -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priorité</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($task->priority === 'urgent') bg-red-100 text-red-800
                                @elseif($task->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>

                        <!-- Date d'échéance -->
                        @if($task->due_date)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date d'échéance</label>
                                <p class="text-sm text-gray-900 {{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-red-600 font-medium' : '' }}">
                                    {{ $task->due_date->format('d/m/Y H:i') }}
                                    @if($task->due_date->isPast() && $task->status !== 'completed')
                                        <span class="block text-xs">(En retard)</span>
                                    @endif
                                </p>
                            </div>
                        @endif

                        <!-- Catégorie -->
                        @if($task->category)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    {{ $task->category->name }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Assignation -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Assignation</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Créé par -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Créé par</label>
                            <p class="text-sm text-gray-900">{{ $task->creator->name ?? 'Utilisateur' }}</p>
                            <p class="text-xs text-gray-500">{{ $task->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <!-- Assigné à -->
                        @if($task->assignee)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Assigné à</label>
                                <p class="text-sm text-gray-900">{{ $task->assignee->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Temps -->
                @if($task->estimated_hours || $task->actual_hours)
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Temps</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @if($task->estimated_hours)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Heures estimées</label>
                                    <p class="text-sm text-gray-900">{{ $task->estimated_hours }}h</p>
                                </div>
                            @endif

                            @if($task->actual_hours)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Heures réelles</label>
                                    <p class="text-sm text-gray-900">{{ $task->actual_hours }}h</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <form action="{{ route('task-manager.tasks.destroy', $task) }}" method="POST"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
