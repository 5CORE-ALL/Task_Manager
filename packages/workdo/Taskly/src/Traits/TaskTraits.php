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
            // Use workspace from the automate task itself, not from session
            $currentWorkspace = $autoMatetask->workspace ?? getActiveWorkSpace();
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
             $autoMatetaskArr['start_date'] =$todayTime;
            $autoMatetaskArr['due_date'] =$todayDueTime;
            $autoMatetaskArr['workspace'] = $currentWorkspace; // Ensure workspace is set
            if($autoMatetask->schedule_type=='weekly')
            {
                $autoMatetaskArr['due_date'] =$weekdayTime;
            }
          
            $autoMatetaskArr['status'] ="Todo";
            unset($autoMatetaskArr['id']); // Remove id to force creation of new task
            unset($autoMatetaskArr['task_id']); // Remove task_id as it's not needed
            
            $newTask = Task::create($autoMatetaskArr);
            
            Log::info('New automated task created', [
                'automate_task_id' => $autoMatetask->id,
                'task_id' => $newTask->id,
                'title' => $autoMatetask->title
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