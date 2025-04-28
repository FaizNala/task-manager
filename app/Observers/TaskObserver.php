<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        $this->updateProjectStatus($task);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        // Hanya trigger jika status task berubah
        if ($task->isDirty('status')) {
            $this->updateProjectStatus($task);
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        $this->updateProjectStatus($task);
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        $this->updateProjectStatus($task);
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        $this->updateProjectStatus($task);
    }

    /**
     * Update project status based on its tasks
     */
    protected function updateProjectStatus(Task $task): void
    {
        // Load the project with its tasks to avoid N+1 problem
        $project = $task->project()->with('tasks')->first();

        if ($project && ($project->auto_update_status ?? true)) {
            $project->updateStatusBasedOnTasks();

            // Use saveQuietly to prevent infinite loop
            $project->saveQuietly();
        }
    }
}
