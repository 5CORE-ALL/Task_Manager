<?php

namespace App\Traits;

use App\Models\TaskActivityReport;
use Illuminate\Support\Facades\Auth;

trait LogsTaskActivity
{
    /**
     * Log task activity
     */
    protected function logTaskActivity($taskName, $activityType, $details = null)
    {
        if (Auth::check()) {
            TaskActivityReport::logActivity(
                $taskName,
                $activityType,
                Auth::user()->name,
                Auth::user()->email,
                request()->ip(),
                $details
            );
        }
    }

    /**
     * Log task creation
     */
    protected function logTaskCreation($taskName, $details = null)
    {
        $this->logTaskActivity($taskName, 'create', $details);
    }

    /**
     * Log task edit
     */
    protected function logTaskEdit($taskName, $details = null)
    {
        $this->logTaskActivity($taskName, 'edit', $details);
    }

    /**
     * Log task deletion
     */
    protected function logTaskDeletion($taskName, $details = null)
    {
        $this->logTaskActivity($taskName, 'delete', $details);
    }

    /**
     * Log task restoration
     */
    protected function logTaskRestoration($taskName, $details = null)
    {
        $this->logTaskActivity($taskName, 'restore', $details);
    }
}
