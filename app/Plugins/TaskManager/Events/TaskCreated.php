<?php

namespace App\Plugins\TaskManager\Events;

use App\Plugins\TaskManager\Models\Task;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Événement déclenché lors de la création d'une tâche
 */
class TaskCreated
{
    use Dispatchable, SerializesModels;

    public Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}
