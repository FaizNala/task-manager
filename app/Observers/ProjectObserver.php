<?php

namespace App\Observers;

use App\Models\Project;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        // Set status awal ketika project dibuat
        if ($project->auto_update_status ?? true) {
            $project->updateStatusBasedOnTasks();
            $project->saveQuietly();
        }
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        // Jika ada perubahan langsung pada status atau auto_update_status
        if ($project->isDirty(['status', 'auto_update_status'])) {
            return;
        }

        // Jika auto update aktif dan ada perubahan pada relasi tasks
        if (($project->auto_update_status ?? true) && $project->tasks()->exists()) {
            $project->updateStatusBasedOnTasks();
            $project->saveQuietly();
        }
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        // Tidak perlu action khusus saat project dihapus
    }

    /**
     * Handle the Project "restored" event.
     */
    public function restored(Project $project): void
    {
        // Update status saat project di-restore
        if ($project->auto_update_status ?? true) {
            $project->updateStatusBasedOnTasks();
            $project->saveQuietly();
        }
    }

    /**
     * Handle the Project "force deleted" event.
     */
    public function forceDeleted(Project $project): void
    {
        // Tidak perlu action khusus saat project di-force delete
    }
}
