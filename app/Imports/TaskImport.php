<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use Workdo\Taskly\Entities\Task;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use App\Traits\SendSmsTraits;

class TaskImport implements ToModel, WithHeadingRow
{
    use SendSmsTraits;
    
    /**
     * @param array $row
     *
     * @return Task|null
     */
    public function model(array $row)
    {
        // Debugging - log the entire row to see what's being imported
        Log::info('Importing row:', $row);

        // Handle dates - more robust parsing
        $startDate = $this->parseDate($row['start_date'] ?? null);
        $dueDate = $this->parseDate($row['due_date'] ?? null);
        
        // Get ETA time - handle different column names
        $etaTime = $row['etc_min'] ?? $row['eta_time'] ?? 0;
        
        // Convert ETA to integer if it comes as string
        $etaTime = is_numeric($etaTime) ? (int)$etaTime : 0;

        if (!empty($row['task']) && !empty($row['assignor']) && !empty($row['assignee'])) {
            $taskData = [
                'title'       => $row['task'],
                'priority'    => $row['priority'] ?? 'medium', // default if not provided
                'group'       => $row['group'] ?? '',
                'start_date'  => $startDate,
                'due_date'    => $dueDate,
                'assign_to'   => $row['assignee'],
                'eta_time'    => $etaTime,
                'description' => $row['description'] ?? '',
                'assignor'    => $row['assignor'],
                'link1'       => $row['link1'] ?? '',
                'link2'       => $row['link2'] ?? '',
                'link3'       => $row['tl'] ?? '',
                'link4'       => $row['vl'] ?? '',
                'link5'       => $row['fl'] ?? '',
                'link7'       => $row['fr'] ?? '',
                'link6'       => $row['cl'] ?? '',
                'workspace'   => getActiveWorkSpace(),
                'status'      => $row['status'] ?? 'Todo', // default if not provided
            ];

            $task = Task::create($taskData);
            
            // Send WhatsApp notification for each imported task
            try {
                // $this->sendSms($task);
                $this->prepareAndQueueNotifications($task);
                Log::info('WhatsApp notification sent for imported task: ' . $task->title);
            } catch (\Exception $e) {
                Log::error('Failed to send WhatsApp notification for imported task: ' . $e->getMessage());
            }
            
            return $task;
        }

        return null;
    }

    /**
     * Parse date from Excel or string format
     */
    protected function parseDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }

        try {
            // Handle Excel date format
            if (is_numeric($dateValue)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue))
                    ->format('Y-m-d');
            }
            
            // Handle string dates (like "06-18-2025")
            return Carbon::createFromFormat('m-d-Y', $dateValue)->format('Y-m-d');
            
        } catch (\Exception $e) {
            Log::error("Failed to parse date: " . $dateValue);
            return null;
        }
    }
}