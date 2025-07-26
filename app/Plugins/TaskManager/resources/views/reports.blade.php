@extends('layouts.app')

@section('title', 'Rapports - Task Manager')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Rapports et statistiques</h1>
            <p class="mt-2 text-gray-600">Analyse détaillée de vos tâches et performances</p>
        </div>

        <!-- Statistiques générales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @if(isset($reportData))
                <!-- Tâches totales -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total des tâches</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $reportData['total_tasks'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Taux de completion -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Taux de completion</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $reportData['completion_rate'] ?? 0 }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Tâches en retard -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Tâches en retard</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $reportData['overdue_tasks'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Heures estimées vs réelles -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Heures totales</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $reportData['total_hours'] ?? 0 }}h</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Placeholder si pas de données -->
                <div class="col-span-4 bg-white rounded-lg shadow p-6">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune donnée disponible</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Créez des tâches pour voir les rapports et statistiques.
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Tâches par statut -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Tâches par statut</h3>
                </div>
                <div class="p-6">
                    @if(isset($reportData['tasks_by_status']) && count($reportData['tasks_by_status']) > 0)
                        <div class="space-y-4">
                            @foreach($reportData['tasks_by_status'] as $status => $count)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full
                                            @if($status === 'completed') bg-green-500
                                            @elseif($status === 'in_progress') bg-yellow-500
                                            @elseif($status === 'cancelled') bg-gray-500
                                            @else bg-blue-500 @endif">
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-900">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Aucune donnée disponible</p>
                    @endif
                </div>
            </div>

            <!-- Tâches par priorité -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Tâches par priorité</h3>
                </div>
                <div class="p-6">
                    @if(isset($reportData['tasks_by_priority']) && count($reportData['tasks_by_priority']) > 0)
                        <div class="space-y-4">
                            @foreach($reportData['tasks_by_priority'] as $priority => $count)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full
                                            @if($priority === 'urgent') bg-red-500
                                            @elseif($priority === 'high') bg-orange-500
                                            @elseif($priority === 'medium') bg-yellow-500
                                            @else bg-green-500 @endif">
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-900">
                                            {{ ucfirst($priority) }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Aucune donnée disponible</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('task-manager.tasks.export', 'xlsx') }}"
                   class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Exporter en Excel
                </a>
                <a href="{{ route('task-manager.tasks.export', 'csv') }}"
                   class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Exporter en CSV
                </a>
                <a href="{{ route('task-manager.tasks.index') }}"
                   class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Voir toutes les tâches
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
