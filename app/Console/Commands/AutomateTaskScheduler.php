<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Workdo\Taskly\Entities\AutomateTask;
use Workdo\Taskly\Traits\TaskTraits;

class AutomateTaskScheduler extends Command
{
    use TaskTraits;

    protected $signature = 'app:automate-task-scheduler';
    protected $description = 'This is a custom scheduler for task scheduler command';

    public function handle()
    {
        // Current time values
        $now = Carbon::now();
        $currentDayToken = $now->format('D'); // Mon, Tue, Wed...
        $currentMinute = $now->format('H:i'); // 14:35 (minute precision)
        $todayDate = (int) $now->day; // 1..31

        Log::debug('AutomateTaskScheduler started', [
            'now' => $now->toDateTimeString(),
            'day_token' => $currentDayToken,
            'current_minute' => $currentMinute,
            'today_date' => $todayDate,
        ]);

        // Helper closure to safely execute found tasks
        $executeTasks = function($tasks, $typeLabel = 'task') {
            foreach ($tasks as $task) {
                try {
                    $this->saveTask($task->id);
                    Log::info("{$typeLabel} executed", ['task_id' => $task->id]);
                } catch (\Exception $e) {
                    Log::error("{$typeLabel} execution failed", [
                        'task_id' => $task->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        };

        // ---------- DAILY ----------
        try {
            $dailyQuery = AutomateTask::where('schedule_type', 'daily')
                ->where('is_pause', 0)
                // match by TIME or DATETIME minute part
                ->whereTime('schedule_time', '=', $currentMinute . ':00'); // matches HH:MM:00
            Log::debug('Daily query SQL', ['sql' => $dailyQuery->toSql(), 'bindings' => $dailyQuery->getBindings()]);
            $dailyTasks = $dailyQuery->get();
            Log::info('Daily tasks found', ['count' => $dailyTasks->count()]);
            $executeTasks($dailyTasks, 'daily');
        } catch (\Exception $ex) {
            Log::error('Daily query failed', ['error' => $ex->getMessage()]);
        }

        // ---------- WEEKLY ----------
        try {
            $weeklyQuery = AutomateTask::where('schedule_type', 'weekly')
                ->where('is_pause', 0)
                // match by minute
                ->whereTime('schedule_time', '=', $currentMinute . ':00')
                // flexible day matcher:
                ->where(function($q) use ($currentDayToken) {
                    // JSON array: ["Mon","Wed"]
                    $q->whereJsonContains('schedule_days', $currentDayToken)
                      // MySQL FIND_IN_SET: works when schedule_days = "Mon,Wed,Sat" (no spaces)
                      ->orWhereRaw('FIND_IN_SET(?, schedule_days)', [$currentDayToken])
                      // comma-wrapped style: ",Mon,Wed," (safe token match)
                      ->orWhere('schedule_days', 'LIKE', "%,{$currentDayToken},%")
                      // plain equality: "Mon" or "Tue"
                      ->orWhere('schedule_days', '=', $currentDayToken);
                });

            Log::debug('Weekly query SQL', ['sql' => $weeklyQuery->toSql(), 'bindings' => $weeklyQuery->getBindings()]);
            $weeklyTasks = $weeklyQuery->get();
            Log::info('Weekly tasks found', ['count' => $weeklyTasks->count()]);
            $executeTasks($weeklyTasks, 'weekly');
        } catch (\Exception $ex) {
            Log::error('Weekly query failed', ['error' => $ex->getMessage()]);
        }

  // ---------- MONTHLY (supports "End of Month") ----------
  try {
    $todayToken = (string) $todayDate;           // e.g. "29"
    $eomToken   = 'End of Month';                // literal token to support

    // Candidate rows: those that mention today's date OR the EOM token.
    // We keep DB-side filtering to limit rows, and then finalize decision in PHP.
    $monthlyQuery = AutomateTask::where('schedule_type', 'monthly')
        ->where('is_pause', 0)
        ->whereTime('schedule_time', '=', $currentMinute . ':00')
        ->where(function($q) use ($todayToken, $eomToken) {
            // match today's numeric token
            $q->where(function($q2) use ($todayToken) {
                $q2->whereJsonContains('schedule_days', $todayToken)
                   ->orWhereRaw('FIND_IN_SET(?, schedule_days)', [$todayToken])
                   ->orWhere('schedule_days', 'LIKE', "%,{$todayToken},%")
                   ->orWhere('schedule_days', '=', $todayToken);
            })
            // OR match the literal "End of Month" token
            ->orWhere(function($q3) use ($eomToken) {
                $q3->whereJsonContains('schedule_days', $eomToken)
                   ->orWhereRaw('FIND_IN_SET(?, schedule_days)', [$eomToken])
                   ->orWhere('schedule_days', 'LIKE', "%,{$eomToken},%")
                   ->orWhere('schedule_days', '=', $eomToken);
            });
        });

    Log::debug('Monthly query SQL', ['sql' => $monthlyQuery->toSql(), 'bindings' => $monthlyQuery->getBindings()]);
    $monthlyCandidates = $monthlyQuery->get();
    Log::info('Monthly candidates found', ['count' => $monthlyCandidates->count()]);

    // Helper: decide whether a specific schedule_days string/array contains a token
    $scheduleContains = function ($scheduleDays, $token) {
        if ($scheduleDays === null) return false;

        // If Laravel casted it to array already, check quickly
        if (is_array($scheduleDays)) {
            return in_array($token, $scheduleDays);
        }

        // try JSON decode (handles '["1","2"]' or '["End of Month"]')
        $decoded = json_decode($scheduleDays, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return in_array($token, $decoded);
        }

        // Normalize string: remove spaces around commas
        $str = trim($scheduleDays);
        $str = str_replace(' ', '', $str);

        // Comma-wrapped token match: ",1,2," or ",End of Month,"
        if (strpos(',' . $str . ',', ',' . $token . ',') !== false) {
            return true;
        }

        // Fallback: explode and check exact parts
        $parts = array_filter(explode(',', $str), function($p) {
            return $p !== '';
        });
        return in_array($token, $parts, true);
    };

    // Helper: last day of month check
    $isLastDayOfMonth = function () use ($now) {
        return $now->day === $now->copy()->endOfMonth()->day;
    };

    $eomToday = $isLastDayOfMonth();

    // Final decision & execution
    foreach ($monthlyCandidates as $task) {
        try {
            $sd = $task->schedule_days; // raw value (string or array depending on your model)
            $hasToday = $scheduleContains($sd, $todayToken);
            $hasEom   = $scheduleContains($sd, $eomToken);

            // execute if today's token is present OR (has EOM token and today is last day)
            if ($hasToday || ($hasEom && $eomToday)) {
                $this->saveTask($task->id);
                Log::info('monthly task executed', ['task_id' => $task->id, 'hasToday' => $hasToday, 'hasEom' => $hasEom]);
            } else {
                Log::debug('monthly task skipped (not matching today)', [
                    'task_id' => $task->id, 'schedule_days' => $sd, 'hasToday' => $hasToday, 'hasEom' => $hasEom, 'eomToday' => $eomToday
                ]);
            }
        } catch (\Exception $e) {
            Log::error('monthly task execution failed', [
                'task_id' => $task->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()
            ]);
        }
    }
} catch (\Exception $ex) {
    Log::error('Monthly query failed', ['error' => $ex->getMessage()]);
}

        Log::debug('AutomateTaskScheduler finished');
    }
}
