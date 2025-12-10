<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Log;
use Workdo\Taskly\Entities\Task;
use Workdo\Taskly\Traits\TaskTraits;

class RemoveAutomateTaskScheduler extends Command
{
    use TaskTraits;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-automate-task-scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is a custom scheduler  for remove task scheduler command';

    /**
     * Execute the console command.
     */
    // public function handle()
    // {
    //     $date = Carbon::now()->toDateTimeString(); // "2025-02-16 12:30:45"
    //     $today = Carbon::today()->toDateString();
    //     $currentTime = Carbon::now()->toTimeString(); // "14:35:20"
    //     $time24HoursBack = Carbon::now()->subDay()->toDateTimeString(); 
    //     $todayDate = Carbon::now()->day; // e.g., 16 for "2025-02-16"
    //     Log::info('time24HoursBack'.$time24HoursBack);
    //     $missedTask = Task::where('is_automate_task',1)->where('is_missed',0)
    //     ->where('due_date', '<', now())->where('status','Todo')->where('deleted_at',NULL)
    //     // ->where('created_at','<=',$time24HoursBack)->where('deleted_at',NULL)
    //     ->update(['is_missed'=>1])
    //     ->update(['is_missed_track'=>1]);
    //     Log::info('missedtask list'.json_encode($missedTask));


    // }
    public function handle()
{
    $now = now(); // uses app timezone

    $updated = Task::where('is_automate_task', 1)
        ->where('is_missed', 0)
        ->where('due_date', '<', $now)
        ->where('status', 'Todo')  
        ->whereNull('deleted_at')
        ->update([
            'is_missed' => 1,
            'is_missed_track' => 1,
        ]);

    \Log::info('Marked missed tasks count: ' . $updated);
}
}
