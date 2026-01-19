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

$filterEmail = 'software2@5core.com';

// Force trigger option: set to true to re-trigger even if task was already created today
$forceTrigger = isset($argv[1]) && ($argv[1] === '--force' || $argv[1] === '-f');

echo "========================================\n";
echo "Automated Tasks Trigger (DEBUG MODE)\n";
echo "⚠ This script ONLY processes AUTOMATED TASKS (from automate_tasks table)\n";
echo "Filter: Only tasks for {$filterEmail}\n";
if ($forceTrigger) {
    echo "⚠ FORCE MODE: Will re-trigger even if already created today\n";
    echo "  (Will delete existing automated task and create a new one)\n";
}
echo "Current Time: " . $now->toDateTimeString() . "\n";
echo "Date Start: " . $todayStart->toDateTimeString() . "\n";
echo "========================================\n\n";

// Get all active automate tasks filtered by email
// NOTE: This ONLY processes tasks from the automate_tasks table, NOT regular manual tasks
// Check if email is in assign_to (comma-separated) or assignor matches
$allTasks = AutomateTask::where('is_pause', 0)
    ->where(function($query) use ($filterEmail) {
        $query->whereRaw("FIND_IN_SET(?, assign_to)", [$filterEmail])
              ->orWhere('assignor', $filterEmail);
    })
    ->get();

echo "Total Active Tasks (filtered for {$filterEmail}): " . $allTasks->count() . "\n\n";

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
        echo "Assign To: " . ($autoTask->assign_to ?? 'NULL') . "\n";
        echo "Assignor: " . ($autoTask->assignor ?? 'NULL') . "\n";
        
        // DEBUG: Check workspace
        $autoTaskWorkspace = $autoTask->workspace;
        echo "DEBUG: AutomateTask workspace = " . ($autoTaskWorkspace ?? 'NULL') . "\n";
        
        if (empty($autoTaskWorkspace)) {
            echo "⚠ WARNING: AutomateTask has no workspace set!\n";
            echo "  This will cause saveTask() to return early without creating a task.\n";
            $failedCount++;
            continue;
        }
        
        // Check if a task was already created today for this automate task (including deleted ones)
        $existingTaskToday = Task::where('automate_task_id', $autoTask->id)
            ->where('created_at', '>=', $todayStart)
            ->where('workspace', $autoTaskWorkspace)
            ->whereNull('deleted_at') // Only non-deleted tasks
            ->first();
        
        // Check for deleted task that should be restored
        $deletedTaskToday = Task::where('automate_task_id', $autoTask->id)
            ->where('created_at', '>=', $todayStart)
            ->where('workspace', $autoTaskWorkspace)
            ->whereNotNull('deleted_at') // Only deleted tasks
            ->first();
        
        if ($existingTaskToday && !$forceTrigger) {
            echo sprintf("SKIP: Already triggered today\n");
            echo "  Existing Task ID: {$existingTaskToday->id}\n";
            echo "  Created At: {$existingTaskToday->created_at}\n";
            echo "  Workspace: {$existingTaskToday->workspace}\n";
            echo "  Status: '{$existingTaskToday->status}'\n";
            echo "  Assign To: " . ($existingTaskToday->assign_to ?? 'NULL') . "\n";
            echo "  Assignor: " . ($existingTaskToday->assignor ?? 'NULL') . "\n";
            echo "  Deleted At: " . ($existingTaskToday->deleted_at ?? 'NULL') . "\n";
            
            // Check if status matches a Stage
            $matchingStage = Stage::where('name', $existingTaskToday->status)
                ->where('workspace_id', $existingTaskToday->workspace)
                ->first();
            
            if ($matchingStage) {
                echo "  ✓ Status '{$existingTaskToday->status}' matches Stage ID: {$matchingStage->id}\n";
            } else {
                echo "  ✗ WARNING: Status '{$existingTaskToday->status}' does NOT match any Stage!\n";
                echo "    This will prevent the task from appearing on the task board.\n";
                $allStages = Stage::where('workspace_id', $existingTaskToday->workspace)->orderBy('order')->get();
                echo "    Available stages in workspace {$existingTaskToday->workspace}:\n";
                foreach ($allStages as $stage) {
                    echo "      - ID: {$stage->id}, Name: '{$stage->name}'\n";
                }
            }
            
            // Check task board visibility
            echo "\n  Checking Task Board Visibility:\n";
            $userEmail = $filterEmail;
            $userBoardQuery = Task::select('tasks.*')
                ->join('stages', 'stages.name', '=', 'tasks.status')
                ->where('tasks.workspace', $existingTaskToday->workspace)
                ->whereNull('tasks.deleted_at')
                ->where(function($query) {
                    $query->where('is_missed', 0)
                          ->orWhere('is_automate_task', 0);
                })
                ->where(function ($query) use ($userEmail) {
                    $query->whereRaw("FIND_IN_SET(?, assign_to)", [$userEmail])
                          ->orWhere('assignor', $userEmail);
                })
                ->where('tasks.id', $existingTaskToday->id)
                ->first();
            
            if ($userBoardQuery) {
                echo "    ✓ Task SHOULD be visible on task board for {$userEmail}\n";
            } else {
                echo "    ✗ Task NOT visible on task board for {$userEmail}\n";
                echo "      Checking why...\n";
                
                // Check base query without user filter
                $baseQuery = Task::select('tasks.*')
                    ->join('stages', 'stages.name', '=', 'tasks.status')
                    ->where('tasks.workspace', $existingTaskToday->workspace)
                    ->whereNull('tasks.deleted_at')
                    ->where(function($query) {
                        $query->where('is_missed', 0)
                              ->orWhere('is_automate_task', 0);
                    })
                    ->where('tasks.id', $existingTaskToday->id)
                    ->first();
                
                if (!$baseQuery) {
                    echo "      - Base query failed (Stage join or other filter issue)\n";
                } else {
                    echo "      - Base query passed, but user filter failed\n";
                    echo "        Task assign_to: " . ($existingTaskToday->assign_to ?? 'NULL') . "\n";
                    echo "        Task assignor: " . ($existingTaskToday->assignor ?? 'NULL') . "\n";
                }
            }
            
            $skippedCount++;
            continue;
        } elseif ($deletedTaskToday) {
            // Restore deleted task instead of creating new one
            echo "⚠ Found deleted task, restoring it...\n";
            echo "  Deleted Task ID: {$deletedTaskToday->id}\n";
            echo "  Deleted At: {$deletedTaskToday->deleted_at}\n";
            echo "  Restoring task (setting deleted_at to NULL)...\n";
            
            $deletedTaskToday->deleted_at = null; // Restore by clearing deleted_at
            $deletedTaskToday->is_missed = 0; // Ensure it's not marked as missed
            $deletedTaskToday->save();
            
            echo "  ✓ Task restored successfully\n";
            echo "  Task ID: {$deletedTaskToday->id}\n";
            echo "  Status: {$deletedTaskToday->status}\n";
            echo "  Assign To: " . ($deletedTaskToday->assign_to ?? 'NULL') . "\n";
            echo "  Assignor: " . ($deletedTaskToday->assignor ?? 'NULL') . "\n";
            echo "  Is Missed: {$deletedTaskToday->is_missed}\n";
            
            $createdCount++;
            $triggeredCount++;
            continue; // Skip to next task since we restored this one
        } elseif ($existingTaskToday && $forceTrigger) {
            echo "⚠ FORCE MODE: Task already exists today, deleting it to re-trigger\n";
            echo "  Existing Task ID: {$existingTaskToday->id}\n";
            echo "  Deleting existing task...\n";
            $existingTaskToday->delete();
            echo "  ✓ Existing task deleted\n";
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

// Fix existing tasks that have is_missed=1 (they won't appear on task board)
echo "\nFIXING EXISTING TASKS: Updating tasks with is_automate_task=1 AND is_missed=1...\n";
$brokenTasks = Task::where('is_automate_task', 1)
    ->where('is_missed', 1)
    ->whereNull('deleted_at')
    ->get();

if ($brokenTasks->count() > 0) {
    echo "Found {$brokenTasks->count()} task(s) that need fixing:\n";
    $fixedCount = 0;
    foreach ($brokenTasks as $brokenTask) {
        echo "  Fixing Task ID: {$brokenTask->id} (AutomateTask ID: {$brokenTask->automate_task_id})\n";
        $brokenTask->is_missed = 0;
        $brokenTask->save();
        $fixedCount++;
    }
    echo "✓ Fixed {$fixedCount} task(s). They should now appear on the task board.\n";
} else {
    echo "No tasks need fixing.\n";
}

// Additional diagnostic query
echo "\nDIAGNOSTIC: Checking all tasks created today for {$filterEmail}...\n";
$todayTasks = Task::where('created_at', '>=', $todayStart)
    ->whereNotNull('automate_task_id')
    ->where(function($query) use ($filterEmail) {
        $query->whereRaw("FIND_IN_SET(?, assign_to)", [$filterEmail])
              ->orWhere('assignor', $filterEmail);
    })
    ->get();
    
echo "Total automated tasks created today for {$filterEmail}: " . $todayTasks->count() . "\n";
foreach ($todayTasks as $task) {
    echo "\n" . str_repeat("-", 60) . "\n";
    echo "TASK ID: {$task->id}\n";
    echo "  AutomateTask ID: {$task->automate_task_id}\n";
    echo "  Workspace: {$task->workspace}\n";
    echo "  Status: '{$task->status}'\n";
    echo "  Assign To: " . ($task->assign_to ?? 'NULL') . "\n";
    echo "  Assignor: " . ($task->assignor ?? 'NULL') . "\n";
    echo "  Deleted At: " . ($task->deleted_at ?? 'NULL') . "\n";
    echo "  Is Automate Task: " . ($task->is_automate_task ?? 'NULL') . "\n";
    echo "  Is Missed: " . ($task->is_missed ?? 'NULL') . "\n";
    
    // Check if status matches a Stage
    $matchingStage = Stage::where('name', $task->status)
        ->where('workspace_id', $task->workspace)
        ->first();
    
    if ($matchingStage) {
        echo "  ✓ Status '{$task->status}' matches Stage ID: {$matchingStage->id}\n";
    } else {
        echo "  ✗ WARNING: Status '{$task->status}' does NOT match any Stage in workspace {$task->workspace}!\n";
        echo "    This will prevent the task from appearing on the task board.\n";
        echo "    Available stages in workspace {$task->workspace}:\n";
        $allStages = Stage::where('workspace_id', $task->workspace)->orderBy('order')->get();
        foreach ($allStages as $stage) {
            echo "      - ID: {$stage->id}, Name: '{$stage->name}'\n";
        }
    }
    
    // Check task board query (matching the actual task board logic)
    echo "\n  Checking Task Board Visibility:\n";
    
    // Base query matching task board
    $taskBoardQuery = Task::select('tasks.*')
        ->join('stages', 'stages.name', '=', 'tasks.status')
        ->where('tasks.workspace', $task->workspace)
        ->whereNull('tasks.deleted_at')
        ->where(function($query) {
            $query->where('is_missed', 0)
                  ->orWhere('is_automate_task', 0);
        })
        ->where('tasks.id', $task->id)
        ->first();
    
    if ($taskBoardQuery) {
        echo "    ✓ Task found in base task board query (with Stage join)\n";
        
        // Check user-specific filter (for software2@5core.com)
        $userEmail = $filterEmail;
        $userBoardQuery = Task::select('tasks.*')
            ->join('stages', 'stages.name', '=', 'tasks.status')
            ->where('tasks.workspace', $task->workspace)
            ->whereNull('tasks.deleted_at')
            ->where(function($query) {
                $query->where('is_missed', 0)
                      ->orWhere('is_automate_task', 0);
            })
            ->where(function ($query) use ($userEmail) {
                $query->whereRaw("FIND_IN_SET(?, assign_to)", [$userEmail])
                      ->orWhere('assignor', $userEmail);
            })
            ->where('tasks.id', $task->id)
            ->first();
        
        if ($userBoardQuery) {
            echo "    ✓ Task visible to user {$userEmail} (assign_to/assignor match)\n";
        } else {
            echo "    ✗ Task NOT visible to user {$userEmail} (assign_to/assignor mismatch)\n";
            echo "      Task assign_to: " . ($task->assign_to ?? 'NULL') . "\n";
            echo "      Task assignor: " . ($task->assignor ?? 'NULL') . "\n";
        }
    } else {
        echo "    ✗ Task NOT found in base task board query!\n";
        echo "      Possible reasons:\n";
        
        // Check deleted
        if ($task->deleted_at) {
            echo "        - Task is soft-deleted (deleted_at: {$task->deleted_at})\n";
        }
        
        // Check workspace
        if ($task->workspace != $task->workspace) {
            echo "        - Workspace mismatch\n";
        }
        
        // Check is_missed filter
        if ($task->is_automate_task == 1 && $task->is_missed == 1) {
            echo "        - Task is automate missed (is_automate_task=1 AND is_missed=1) - excluded from board\n";
        }
        
        // Check stage join
        if (!$matchingStage) {
            echo "        - Status '{$task->status}' doesn't match any Stage name (Stage join fails)\n";
        }
    }
}
