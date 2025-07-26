<?php

namespace App\Plugins\TaskManager\Services;

use App\Plugins\TaskManager\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

/**
 * Service de gestion des notifications
 */
class NotificationService
{
    /**
     * Envoie une notification de création de tâche.
     */
    public function sendTaskCreatedNotification(Task $task): void
    {
        if ($task->assigned_to && $task->assigned_to !== $task->created_by) {
            $assignee = User::find($task->assigned_to);
            if ($assignee) {
                // Envoyer notification à l'assigné
                // Notification::send($assignee, new TaskAssignedNotification($task));
            }
        }
    }

    /**
     * Envoie une notification d'assignation de tâche.
     */
    public function sendTaskAssignedNotification(Task $task, User $assignee): void
    {
        // Notification::send($assignee, new TaskAssignedNotification($task));
    }

    /**
     * Envoie une notification de completion de tâche.
     */
    public function sendTaskCompletedNotification(Task $task): void
    {
        if ($task->created_by && $task->created_by !== $task->assigned_to) {
            $creator = User::find($task->created_by);
            if ($creator) {
                // Notification::send($creator, new TaskCompletedNotification($task));
            }
        }
    }

    /**
     * Envoie une notification de mise à jour de tâche.
     */
    public function sendTaskUpdatedNotification(Task $task): void
    {
        // Logique pour notifier les parties prenantes
    }

    /**
     * Envoie une notification de suppression de tâche.
     */
    public function sendTaskDeletedNotification(Task $task): void
    {
        // Logique pour notifier les parties prenantes
    }
}
