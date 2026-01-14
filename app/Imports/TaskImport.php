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
     * Supports DD/MM/YYYY format (e.g., 12/01/2026 = 12th January 2026)
     */
    protected function parseDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }

        try {
            // Handle Excel date format (numeric value)
            if (is_numeric($dateValue)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue))
                    ->format('Y-m-d');
            }
            
            // Convert to string if not already
            $dateString = trim((string) $dateValue);
            
            // For dates in format like "12/01/2026", parse as DD/MM/YYYY
            // Check if it matches DD/MM/YYYY or DD-MM-YYYY pattern
            if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/', $dateString, $matches)) {
                $day = (int) $matches[1];
                $month = (int) $matches[2];
                $year = (int) $matches[3];
                
                // Validate and create date as DD/MM/YYYY (day, month, year)
                if ($day >= 1 && $day <= 31 && $month >= 1 && $month <= 12) {
                    try {
                        $date = Carbon::create($year, $month, $day);
                        return $date->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Invalid date (e.g., Feb 30), continue to other formats
                    }
                }
            }
            
            // Try DD/MM/YYYY formats explicitly
            $formats = [
                'd/m/Y',    // DD/MM/YYYY (e.g., 12/01/2026 = 12th January)
                'd-m-Y',    // DD-MM-YYYY (e.g., 12-01-2026 = 12th January)
                'd.m.Y',    // DD.MM.YYYY (e.g., 12.01.2026 = 12th January)
                'Y-m-d',    // YYYY-MM-DD (ISO format)
            ];
            
            foreach ($formats as $format) {
                try {
                    $date = Carbon::createFromFormat($format, $dateString);
                    return $date->format('Y-m-d');
                } catch (\Exception $e) {
                    // Try next format
                    continue;
                }
            }
            
            // Fallback: try Carbon's default parsing
            return Carbon::parse($dateString)->format('Y-m-d');
            
        } catch (\Exception $e) {
            Log::error("Failed to parse date: " . $dateValue . " - " . $e->getMessage());
            return null;
        }
    }
}