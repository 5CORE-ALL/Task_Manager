<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Workdo\Taskly\Entities\AutomateTask;
use Workdo\Taskly\Entities\Task;
use Workdo\Taskly\Traits\TaskTraits;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

// Create a temporary class to use the trait
class TempTaskTrigger {
    use TaskTraits;
    
    public function triggerTask($autoMatetaskId) {
        return $this->saveTask($autoMatetaskId);
    }
}

$trigger = new TempTaskTrigger();

$now = Carbon::now();
$todayStart = $now->copy()->startOfDay();

echo "========================================\n";
echo "One-Time Task Trigger (Testing)\n";
echo "Current Time: " . $now->toDateTimeString() . "\n";
echo "========================================\n\n";

// Get all active automate tasks
$allTasks = AutomateTask::where('is_pause', 0)->get();

echo "Total Active Tasks: " . $allTasks->count() . "\n\n";

$triggeredCount = 0;
$skippedCount = 0;
$errorCount = 0;

foreach ($allTasks as $autoTask) {
    try {
        // Check if a task was already created today for this automate task
        $currentWorkspace = $autoTask->workspace ?? getActiveWorkSpace();
        
        $existingTaskToday = Task::where('automate_task_id', $autoTask->id)
            ->where('created_at', '>=', $todayStart)
            ->where('workspace', $currentWorkspace)
            ->first();
        
        if ($existingTaskToday) {
            echo sprintf("SKIP: Task ID %d - Already triggered today (Task #%d created at %s)\n", 
                $autoTask->id, 
                $existingTaskToday->id,
                $existingTaskToday->created_at
            );
            $skippedCount++;
            continue;
        }
        
        // Trigger the task
        echo sprintf("TRIGGERING: Task ID %d - %s (Type: %s, Time: %s)\n", 
            $autoTask->id,
            substr($autoTask->title, 0, 50),
            $autoTask->schedule_type,
            $autoTask->schedule_time ?? 'N/A'
        );
        
        $trigger->triggerTask($autoTask->id);
        $triggeredCount++;
        
        echo "  ✓ Successfully triggered\n";
        
    } catch (\Exception $e) {
        echo sprintf("ERROR: Task ID %d - %s\n", $autoTask->id, $e->getMessage());
        $errorCount++;
        Log::error('One-time trigger failed', [
            'task_id' => $autoTask->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

echo "\n========================================\n";
echo "Summary:\n";
echo "  Triggered: $triggeredCount\n";
echo "  Skipped (already triggered today): $skippedCount\n";
echo "  Errors: $errorCount\n";
echo "========================================\n";
