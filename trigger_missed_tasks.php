<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Workdo\Taskly\Entities\AutomateTask;
use Workdo\Taskly\Entities\Task;
use Workdo\Taskly\Entities\Stage;
use Workdo\Taskly\Traits\TaskTraits;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
echo "One-Time Task Trigger (DEBUG MODE)\n";
echo "Current Time: " . $now->toDateTimeString() . "\n";
echo "Date Start: " . $todayStart->toDateTimeString() . "\n";
echo "========================================\n\n";

// Get all active automate tasks
$allTasks = AutomateTask::where('is_pause', 0)->get();

echo "Total Active Tasks: " . $allTasks->count() . "\n\n";

$triggeredCount = 0;
$skippedCount = 0;
$errorCount = 0;
$createdCount = 0;
$failedCount = 0;

foreach ($allTasks as $autoTask) {
    try {
        echo "\n" . str_repeat("-", 60) . "\n";
        echo "Processing AutomateTask ID: {$autoTask->id}\n";
        echo "Title: " . substr($autoTask->title, 0, 60) . "\n";
        
        // DEBUG: Check workspace
        $autoTaskWorkspace = $autoTask->workspace;
        echo "DEBUG: AutomateTask workspace = " . ($autoTaskWorkspace ?? 'NULL') . "\n";
        
        if (empty($autoTaskWorkspace)) {
            echo "⚠ WARNING: AutomateTask has no workspace set!\n";
            echo "  This will cause saveTask() to return early without creating a task.\n";
            $failedCount++;
            continue;
        }
        
        // Check if a task was already created today for this automate task
        $existingTaskToday = Task::where('automate_task_id', $autoTask->id)
            ->where('created_at', '>=', $todayStart)
            ->where('workspace', $autoTaskWorkspace)
            ->first();
        
        if ($existingTaskToday) {
            echo sprintf("SKIP: Already triggered today\n");
            echo "  Existing Task ID: {$existingTaskToday->id}\n";
            echo "  Created At: {$existingTaskToday->created_at}\n";
            echo "  Workspace: {$existingTaskToday->workspace}\n";
            $skippedCount++;
            continue;
        }
        
        // DEBUG: Check Stage availability
        $firstStage = Stage::where('workspace_id', '=', $autoTaskWorkspace)->orderBy('order')->first();
        if (!$firstStage) {
            echo "⚠ WARNING: No Stage found for workspace {$autoTaskWorkspace}\n";
            echo "  Task will be created with status 'Todo' as fallback\n";
        } else {
            echo "DEBUG: First Stage found - ID: {$firstStage->id}, Name: '{$firstStage->name}'\n";
        }
        
        // Get task count before
        $taskCountBefore = Task::where('automate_task_id', $autoTask->id)->count();
        echo "DEBUG: Tasks with this automate_task_id before: {$taskCountBefore}\n";
        
        // Trigger the task
        echo "TRIGGERING: Calling saveTask()...\n";
        
        $result = $trigger->triggerTask($autoTask->id);
        
        // DEBUG: Verify task was created
        $taskCountAfter = Task::where('automate_task_id', $autoTask->id)->count();
        echo "DEBUG: Tasks with this automate_task_id after: {$taskCountAfter}\n";
        
        // Find the newly created task
        $newTask = Task::where('automate_task_id', $autoTask->id)
            ->where('created_at', '>=', $todayStart)
            ->where('workspace', $autoTaskWorkspace)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($newTask) {
            echo "✓ TASK CREATED SUCCESSFULLY!\n";
            echo "  New Task ID: {$newTask->id}\n";
            echo "  Title: {$newTask->title}\n";
            echo "  Workspace: {$newTask->workspace}\n";
            echo "  Status: {$newTask->status}\n";
            echo "  Assign To: " . ($newTask->assign_to ?? 'NULL') . "\n";
            echo "  Assignor: " . ($newTask->assignor ?? 'NULL') . "\n";
            echo "  Created At: {$newTask->created_at}\n";
            echo "  Is Automate Task: " . ($newTask->is_automate_task ?? 'NULL') . "\n";
            
            // Verify task is queryable with same filters as task board
            $taskBoardQuery = Task::where('workspace', $autoTaskWorkspace)
                ->whereNull('deleted_at')
                ->where('id', $newTask->id)
                ->first();
            
            if ($taskBoardQuery) {
                echo "  ✓ Task is visible in task board query (workspace match, not deleted)\n";
            } else {
                echo "  ⚠ WARNING: Task NOT found in task board query!\n";
                echo "    This means it won't appear on the task board page.\n";
                echo "    Checking why...\n";
                
                $checkDeleted = Task::where('id', $newTask->id)->whereNotNull('deleted_at')->first();
                if ($checkDeleted) {
                    echo "    ✗ Task is soft-deleted (deleted_at: {$checkDeleted->deleted_at})\n";
                }
                
                $checkWorkspace = Task::where('id', $newTask->id)->where('workspace', '!=', $autoTaskWorkspace)->first();
                if ($checkWorkspace) {
                    echo "    ✗ Task workspace mismatch (expected: {$autoTaskWorkspace}, got: {$checkWorkspace->workspace})\n";
                }
            }
            
            $createdCount++;
            $triggeredCount++;
        } else {
            echo "✗ TASK CREATION FAILED!\n";
            echo "  saveTask() returned but no task was created in database.\n";
            echo "  Possible reasons:\n";
            echo "    - Workspace validation failed in saveTask()\n";
            echo "    - Duplicate check prevented creation\n";
            echo "    - Database error (check for exceptions above)\n";
            echo "    - Task creation silently failed\n";
            
            // Check if task exists with different workspace
            $taskOtherWorkspace = Task::where('automate_task_id', $autoTask->id)
                ->where('created_at', '>=', $todayStart)
                ->where('workspace', '!=', $autoTaskWorkspace)
                ->first();
            
            if ($taskOtherWorkspace) {
                echo "  ⚠ Found task created with different workspace: {$taskOtherWorkspace->workspace}\n";
            }
            
            $failedCount++;
        }
        
    } catch (\Exception $e) {
        echo "✗ EXCEPTION: " . $e->getMessage() . "\n";
        echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "  Trace:\n" . $e->getTraceAsString() . "\n";
        $errorCount++;
        Log::error('One-time trigger failed', [
            'task_id' => $autoTask->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "SUMMARY:\n";
echo "  Total Processed: " . $allTasks->count() . "\n";
echo "  Successfully Created: {$createdCount}\n";
echo "  Triggered (reported): {$triggeredCount}\n";
echo "  Failed to Create: {$failedCount}\n";
echo "  Skipped (already triggered today): {$skippedCount}\n";
echo "  Errors/Exceptions: {$errorCount}\n";
echo str_repeat("=", 60) . "\n";

// Additional diagnostic query
echo "\nDIAGNOSTIC: Checking all tasks created today...\n";
$todayTasks = Task::where('created_at', '>=', $todayStart)
    ->whereNotNull('automate_task_id')
    ->get();
    
echo "Total automated tasks created today: " . $todayTasks->count() . "\n";
foreach ($todayTasks as $task) {
    echo "  Task ID: {$task->id}, AutomateTask ID: {$task->automate_task_id}, Workspace: {$task->workspace}, Status: {$task->status}\n";
}
