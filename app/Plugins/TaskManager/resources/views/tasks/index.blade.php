<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - Liste des Tâches</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">📋 Task Manager</h1>
                <a href="{{ route('task-manager.tasks.create') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    ➕ Nouvelle Tâche
                </a>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total_tasks'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Total Tâches</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['completed_tasks'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Terminées</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_tasks'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">En Attente</div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-red-600">{{ $stats['overdue_tasks'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">En Retard</div>
                </div>
            </div>

            <!-- Liste des tâches -->
            <div class="space-y-4">
                @forelse($tasks as $task)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-800">{{ $task->title }}</h3>
                                <p class="text-gray-600 mt-1">{{ Str::limit($task->description, 100) }}</p>

                                <div class="flex items-center gap-4 mt-3">
                                    <!-- Statut -->
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($task->status === 'completed') bg-green-100 text-green-800
                                        @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                        @elseif($task->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($task->status) }}
                                    </span>

                                    <!-- Priorité -->
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($task->priority === 'urgent') bg-red-100 text-red-800
                                        @elseif($task->priority === 'high') bg-orange-100 text-orange-800
                                        @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($task->priority) }}
                                    </span>

                                    <!-- Catégorie -->
                                    @if($task->category)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $task->category->name }}
                                        </span>
                                    @endif

                                    <!-- Date d'échéance -->
                                    @if($task->due_date)
                                        <span class="text-sm text-gray-500">
                                            📅 {{ $task->due_date->format('d/m/Y') }}
                                            @if($task->is_overdue)
                                                <span class="text-red-500">(En retard)</span>
                                            @endif
                                        </span>
                                    @endif
                                </div>

                                <!-- Assigné à -->
                                @if($task->assignee)
                                    <div class="mt-2 text-sm text-gray-600">
                                        👤 Assigné à: {{ $task->assignee->name }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('task-manager.tasks.show', $task) }}"
                                   class="text-blue-500 hover:text-blue-700">👁️</a>
                                <a href="{{ route('task-manager.tasks.edit', $task) }}"
                                   class="text-green-500 hover:text-green-700">✏️</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="text-4xl mb-4">📝</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune tâche trouvée</h3>
                        <p class="text-gray-600">Commencez par créer votre première tâche !</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($tasks->hasPages())
                <div class="mt-6">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
