<?php
namespace Workdo\Taskly\Traits;
use Google\Service\SecretManager\Automatic;
use Illuminate\Support\Facades\Log;
use Workdo\Taskly\Entities\AutomateTask;
use Workdo\Taskly\Entities\Task;
use Carbon\Carbon;
use Workdo\Taskly\Entities\Stage;

trait TaskTraits
{
    function saveTask($autoMatetaskId)
    {
        $autoMatetask = AutomateTask::find($autoMatetaskId);
        if ($autoMatetask) {
             $todayTime = Carbon::now()->toDateTimeString(); 
            $todayStart = Carbon::now()->startOfDay();
            // Use workspace from the automate task itself (required for cron/scheduler)
            // Never use getActiveWorkSpace() as it requires authenticated user which doesn't exist in cron
            $currentWorkspace = $autoMatetask->workspace;
            
            // Validate workspace exists
            if (empty($currentWorkspace)) {
                Log::error('AutomateTask missing workspace', [
                    'automate_task_id' => $autoMatetask->id,
                    'title' => $autoMatetask->title
                ]);
                return; // Cannot create task without workspace
            }
            $weekdayTime = Carbon::now()->addDays(7)->toDateTimeString();
            $todayDueTime = Carbon::now()->addDays(1)->toDateTimeString();
            
            // Check if a task was already created today for this automate task
            // This prevents creating duplicate tasks on the same day
            $existingTaskToday = Task::where('automate_task_id', $autoMatetask->id)
                ->where('created_at', '>=', $todayStart)
                ->where('workspace', $currentWorkspace)
                ->first();
                
            if ($existingTaskToday) {
                Log::info('Task already created today for automate task', [
                    'automate_task_id' => $autoMatetask->id,
                    'task_id' => $existingTaskToday->id,
                    'created_at' => $existingTaskToday->created_at
                ]);
                return; // Don't create duplicate
            }
            
            $autoMatetaskArr = $autoMatetask->toArray();
            $autoMatetaskArr['task_type'] ="automate_task";
            $autoMatetaskArr['automate_task_id'] = $autoMatetask->id;
            $autoMatetaskArr['is_automate_task'] =1;
            $autoMatetaskArr['is_missed'] = 0; // Set to 0 so task appears on task board (tasks with is_automate_task=1 AND is_missed=1 are excluded)
            $autoMatetaskArr['start_date'] = $todayTime;
            $autoMatetaskArr['workspace'] = $currentWorkspace; // Ensure workspace is set
            
            // Set a fresh due_date to prevent cron from marking as missed immediately
            // Use the automate task's due_date if it's in the future, otherwise set to tomorrow
            if (!empty($autoMatetask->due_date)) {
                $automateDueDate = Carbon::parse($autoMatetask->due_date);
                // If automate task due_date is in the future, use it; otherwise set to tomorrow
                if ($automateDueDate->isFuture()) {
                    $autoMatetaskArr['due_date'] = $automateDueDate->toDateTimeString();
                } else {
                    $autoMatetaskArr['due_date'] = $todayDueTime; // Tomorrow by default
                }
            } else {
                $autoMatetaskArr['due_date'] = $todayDueTime; // Tomorrow by default
            }
            
            // For weekly tasks, set due_date to next week
            if (strtolower($autoMatetask->schedule_type ?? '') === 'weekly') {
                $autoMatetaskArr['due_date'] = $weekdayTime; // 7 days from now
            }
          
            // Get the first stage dynamically to ensure proper status matching
            $firstStage = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->first();
            $firstStageName = $firstStage ? $firstStage->name : 'Todo';
            $autoMatetaskArr['status'] = $firstStageName;
            unset($autoMatetaskArr['id']); // Remove id to force creation of new task
            unset($autoMatetaskArr['task_id']); // Remove task_id as it's not needed
            
            $newTask = Task::create($autoMatetaskArr);
            
            // Verify assign_to/assignor were copied correctly
            $taskAssignTo = $newTask->assign_to ?? 'NULL';
            $taskAssignor = $newTask->assignor ?? 'NULL';
            $autoAssignTo = $autoMatetask->assign_to ?? 'NULL';
            $autoAssignor = $autoMatetask->assignor ?? 'NULL';
            
            Log::info('New automated task created', [
                'automate_task_id' => $autoMatetask->id,
                'task_id' => $newTask->id,
                'title' => $autoMatetask->title,
                'workspace' => $currentWorkspace,
                'status' => $firstStageName,
                'automate_task_assign_to' => $autoAssignTo,
                'automate_task_assignor' => $autoAssignor,
                'task_assign_to' => $taskAssignTo,
                'task_assignor' => $taskAssignor,
                'assign_to_copied' => ($taskAssignTo === $autoAssignTo) ? 'YES' : 'NO',
                'assignor_copied' => ($taskAssignor === $autoAssignor) ? 'YES' : 'NO',
                'stage_name' => $firstStageName
            ]);
        }

    }
     function duplicateTask($taskId)
    {
        Log::info("Method call from duplicate task".$taskId);
        $autoMatetask = Task::find($taskId);
        if ($autoMatetask) {

            $autoMatetaskArr = $autoMatetask->toArray();
            $autoMatetaskArr['id'] = NULL;
         return  Task::create(
                $autoMatetaskArr // Data to update or insert
            );
        }

    } 
}