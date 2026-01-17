<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Workdo\Taskly\Entities\Task;
use Workdo\Taskly\Entities\AutomateTask;
use Workdo\Taskly\Entities\Stage;
use Carbon\Carbon;

echo "========================================\n";
echo "Automated Task Diagnostic Check\n";
echo "Current Time: " . Carbon::now()->toDateTimeString() . "\n";
echo "========================================\n\n";

// Get recent automated tasks (last 24 hours)
$todayStart = Carbon::now()->startOfDay();
$recentTasks = Task::where('is_automate_task', 1)
    ->where('created_at', '>=', $todayStart->copy()->subDay())
    ->orderBy('created_at', 'desc')
    ->get();

echo "Recent Automated Tasks (last 24 hours): " . $recentTasks->count() . "\n\n";

foreach ($recentTasks as $task) {
    echo "Task ID: {$task->id}\n";
    echo "  Title: " . substr($task->title, 0, 50) . "\n";
    echo "  Workspace: {$task->workspace}\n";
    echo "  Status: {$task->status}\n";
    echo "  Assign To: " . ($task->assign_to ?? 'N/A') . "\n";
    echo "  Assignor: " . ($task->assignor ?? 'N/A') . "\n";
    echo "  Created At: {$task->created_at}\n";
    
    // Check if status matches a stage
    $stage = Stage::where('workspace_id', $task->workspace)
        ->where('name', $task->status)
        ->first();
    
    if ($stage) {
        echo "  ✓ Status matches stage: {$stage->name} (ID: {$stage->id})\n";
    } else {
        echo "  ✗ Status '{$task->status}' does NOT match any stage in workspace {$task->workspace}\n";
        // List available stages
        $stages = Stage::where('workspace_id', $task->workspace)->pluck('name')->toArray();
        echo "    Available stages: " . implode(', ', $stages) . "\n";
    }
    
    // Check if task is deleted
    if ($task->deleted_at) {
        echo "  ⚠ Task is soft-deleted: {$task->deleted_at}\n";
    }
    
    echo "\n";
}

// Check automate tasks configuration
echo "\n========================================\n";
echo "Automate Tasks Configuration:\n";
echo "========================================\n\n";

$autoTasks = AutomateTask::where('is_pause', 0)->get();
echo "Active Automate Tasks: " . $autoTasks->count() . "\n\n";

foreach ($autoTasks as $autoTask) {
    echo "AutoTask ID: {$autoTask->id}\n";
    echo "  Title: " . substr($autoTask->title, 0, 50) . "\n";
    echo "  Workspace: {$autoTask->workspace}\n";
    echo "  Type: {$autoTask->schedule_type}\n";
    echo "  Time: " . ($autoTask->schedule_time ?? 'N/A') . "\n";
    echo "  Assign To: " . ($autoTask->assign_to ?? 'N/A') . "\n";
    echo "  Assignor: " . ($autoTask->assignor ?? 'N/A') . "\n";
    
    // Check if task was created today
    $todayTask = Task::where('automate_task_id', $autoTask->id)
        ->where('created_at', '>=', $todayStart)
        ->first();
    
    if ($todayTask) {
        echo "  ✓ Task created today: Task ID {$todayTask->id}\n";
    } else {
        echo "  ✗ No task created today\n";
    }
    echo "\n";
}

echo "========================================\n";
echo "Check Complete\n";
echo "========================================\n";