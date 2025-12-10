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
            $currentWorkspace = getActiveWorkSpace();
            $weekdayTime = Carbon::now()->addDays(7)->toDateTimeString();
            $todayDueTime = Carbon::now()->addDays(1)->toDateTimeString();
            $autoMatetaskArr = $autoMatetask->toArray();
            $autoMatetaskArr['task_type'] ="automate_task";
            $autoMatetaskArr['automate_task_id'] = $autoMatetask->id;
            $autoMatetaskArr['is_automate_task'] =1;
             $autoMatetaskArr['start_date'] =$todayTime;
            $autoMatetaskArr['due_date'] =$todayDueTime;
            if($autoMatetask->schedule_type=='weekly')
            {
                $autoMatetaskArr['due_date'] =$weekdayTime;
            }
          
            $autoMatetaskArr['status'] ="Todo";
            Task::updateOrCreate(
                ['id' => $autoMatetask->task_id], // Search criteria (modify based on your needs)
                $autoMatetaskArr // Data to update or insert
            );
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