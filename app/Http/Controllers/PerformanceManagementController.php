<?php

namespace App\Http\Controllers;

use App\Models\PerformanceManagement;
use App\Models\PerformanceFeedback;
use App\Models\User;
use App\Models\Dar;
use App\Models\DarTask;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Workdo\Taskly\Entities\Task;

class PerformanceManagementController extends Controller
{
    /**
     * Display a listing of performance records
     */
    public function index(Request $request)
    {
        $workspaceId = getActiveWorkSpace();
        
        // Check if user is privileged (super admin or specific emails)
        $privilegedEmails = [
            'president@5core.com',
            'hr@5core.com',
            'tech-support@5core.com',
            'support@5core.com',
            'mgr-advertisement@5core.com',
            'mgr-content@5core.com',
        ];
        
        $isPrivileged = Auth::user()->type === 'super admin' || in_array(Auth::user()->email, $privilegedEmails);
        
        // Get employees list
        if ($isPrivileged) {
            $employees = User::where('type', '!=', 'super admin')
                ->where('workspace_id', $workspaceId)
                ->get();
        } else {
            $employees = User::where('id', Auth::id())->get();
        }
        
        // Get period filter
        $period = $request->get('period', date('Y-m'));
        $periodType = $request->get('period_type', 'monthly');
        
        // Get performance records
        $query = PerformanceManagement::with(['employee', 'feedbacks'])
            ->where('workspace_id', $workspaceId);
        
        if (!$isPrivileged) {
            $query->where('employee_id', Auth::id());
        }
        
        if ($period) {
            $query->where('period', $period);
        }
        
        $performanceRecords = $query->orderBy('overall_score', 'desc')->get();
        
        return view('performance.index', compact('employees', 'performanceRecords', 'period', 'periodType', 'isPrivileged'));
    }

    /**
     * Generate or retrieve performance data for an employee
     */
    public function generatePerformance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'period' => 'required|string',
            'period_type' => 'required|in:monthly,weekly,quarterly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $workspaceId = getActiveWorkSpace();
        $employeeId = $request->employee_id;
        $employee = User::findOrFail($employeeId);
        
        // Check if record already exists
        $existing = PerformanceManagement::where('employee_id', $employeeId)
            ->where('period', $request->period)
            ->where('period_type', $request->period_type)
            ->where('workspace_id', $workspaceId)
            ->first();
        
        // Calculate metrics (always recalculate to get latest values)
        $metrics = $this->calculateMetrics($employee, $request->start_date, $request->end_date, $workspaceId);
        
        if ($existing) {
            // Update existing record with latest calculated values
            $existing->update([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'etc_hours' => $metrics['etc_hours'],
                'atc_hours' => $metrics['atc_hours'],
                'total_working_hours' => $metrics['total_working_hours'],
                'productive_hours' => $metrics['productive_hours'],
                'tasks_completed' => $metrics['tasks_completed'],
                'avg_task_duration_minutes' => $metrics['avg_task_duration_minutes'],
                'avg_task_duration_days' => $metrics['avg_task_duration_days'],
                'total_tasks_assigned' => $metrics['total_tasks_assigned'],
                'total_tasks_completed' => $metrics['total_tasks_completed'],
                'task_completion_rate' => $metrics['task_completion_rate'],
                'efficiency_score' => $metrics['efficiency_score'],
                'productivity_score' => $metrics['productivity_score'],
                'task_performance_score' => $metrics['task_performance_score'],
                'timeliness_score' => $metrics['timeliness_score'],
                'overall_score' => $metrics['overall_score'],
            ]);
            
            // Refresh to get updated attributes
            $existing->refresh();
            
            return response()->json([
                'success' => true,
                'message' => 'Performance data updated successfully with latest values',
                'data' => $existing
            ]);
        }
        
        // Create new performance record
        $performance = PerformanceManagement::create([
            'employee_id' => $employeeId,
            'user_id' => $employeeId,
            'period_type' => $request->period_type,
            'period' => $request->period,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'etc_hours' => $metrics['etc_hours'],
            'atc_hours' => $metrics['atc_hours'],
            'total_working_hours' => $metrics['total_working_hours'],
            'productive_hours' => $metrics['productive_hours'],
            'tasks_completed' => $metrics['tasks_completed'],
            'avg_task_duration_minutes' => $metrics['avg_task_duration_minutes'],
            'avg_task_duration_days' => $metrics['avg_task_duration_days'],
            'total_tasks_assigned' => $metrics['total_tasks_assigned'],
            'total_tasks_completed' => $metrics['total_tasks_completed'],
            'task_completion_rate' => $metrics['task_completion_rate'],
            'efficiency_score' => $metrics['efficiency_score'],
            'productivity_score' => $metrics['productivity_score'],
            'task_performance_score' => $metrics['task_performance_score'],
            'timeliness_score' => $metrics['timeliness_score'],
            'overall_score' => $metrics['overall_score'],
            'workspace_id' => $workspaceId,
            'created_by' => Auth::id(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Performance data generated successfully',
            'data' => $performance
        ]);
    }

    /**
     * Calculate all performance metrics for an employee
     */
    private function calculateMetrics($employee, $startDate, $endDate, $workspaceId)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        // 1. Get ETC and ATC from tasks
        $etcAtc = $this->getETCAndATC($employee->email, $start, $end, $workspaceId);
        
        // 2. Get total working hours from attendance
        $totalWorkingHours = $this->getTotalWorkingHours($employee->id, $start, $end);
        
        // 3. Get productive hours from DAR
        $productiveHours = $this->getProductiveHours($employee->id, $start, $end);
        
        // 4. Get tasks completed from Task table (status = Done)
        $taskMetrics = $this->getTaskCompletionMetrics($employee->email, $start, $end, $workspaceId);
        
        // Calculate scores
        $efficiencyScore = $this->calculateEfficiencyScore($etcAtc['etc'], $etcAtc['atc']);
        $productivityScore = $this->calculateProductivityScore($productiveHours, $totalWorkingHours);
        $taskPerformanceScore = $this->calculateTaskPerformanceScore($taskMetrics['completion_rate']);
        $timelinessScore = $this->calculateTimelinessScore($taskMetrics['avg_duration_days']);
        
        // Overall score (weighted average)
        $overallScore = (
            $efficiencyScore * 0.25 +
            $productivityScore * 0.25 +
            $taskPerformanceScore * 0.25 +
            $timelinessScore * 0.25
        );
        
        return [
            'etc_hours' => $etcAtc['etc'],
            'atc_hours' => $etcAtc['atc'],
            'total_working_hours' => $totalWorkingHours,
            'productive_hours' => $productiveHours,
            'tasks_completed' => $taskMetrics['total_completed'], // Use tasks with status "Done"
            'avg_task_duration_minutes' => $taskMetrics['avg_duration_minutes'],
            'avg_task_duration_days' => $taskMetrics['avg_duration_days'],
            'total_tasks_assigned' => $taskMetrics['total_assigned'],
            'total_tasks_completed' => $taskMetrics['total_completed'],
            'task_completion_rate' => $taskMetrics['completion_rate'],
            'efficiency_score' => round($efficiencyScore, 2),
            'productivity_score' => round($productivityScore, 2),
            'task_performance_score' => round($taskPerformanceScore, 2),
            'timeliness_score' => round($timelinessScore, 2),
            'overall_score' => round($overallScore, 2),
        ];
    }

    /**
     * Get ETC and ATC hours from tasks
     */
    private function getETCAndATC($employeeEmail, $start, $end, $workspaceId)
    {
        $tasks = Task::where('workspace', $workspaceId)
            ->where(function($query) use ($employeeEmail) {
                $query->where('assign_to', 'LIKE', "%{$employeeEmail}%")
                      ->orWhere('assign_to', $employeeEmail);
            })
            ->whereBetween('created_at', [$start, $end])
            ->get();
        
        $totalETCMinutes = $tasks->where('eta_time', '>', 0)->sum('eta_time');
        $totalATCMinutes = $tasks->where('etc_done', '>', 0)->sum('etc_done');
        
        return [
            'etc' => round($totalETCMinutes / 60, 2),
            'atc' => round($totalATCMinutes / 60, 2),
        ];
    }

    /**
     * Get total working hours from attendance (like salary slip calculation)
     */
    private function getTotalWorkingHours($employeeId, $start, $end)
    {
        // Try to get from payroll table first (if available for the period)
        $month = $start->format('F Y');
        $payroll = Payroll::where('employee_id', $employeeId)
            ->where('month', $month)
            ->first();
        
        if ($payroll && $payroll->total_hours > 0) {
            return round($payroll->total_hours, 2);
        }
        
        // Fallback to attendance calculation
        $attendances = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->where('status', 'Present')
            ->whereNotNull('clock_in')
            ->get();
        
        $totalMinutes = 0;
        foreach ($attendances as $attendance) {
            if (!$attendance->clock_in) continue;
            
            $clockIn = Carbon::parse($attendance->date . ' ' . $attendance->clock_in);
            
            // If no clock_out or clock_out is 00:00:00, use end of day or current time
            if (!$attendance->clock_out || $attendance->clock_out == '00:00:00') {
                // Use end of working day (e.g., 18:00) or current time if same day
                $attendanceDate = Carbon::parse($attendance->date);
                if ($attendanceDate->isToday()) {
                    $clockOut = Carbon::now();
                } else {
                    $clockOut = $attendanceDate->copy()->setTime(18, 0, 0); // Default 8 hours
                }
            } else {
                $clockOut = Carbon::parse($attendance->date . ' ' . $attendance->clock_out);
            }
            
            $totalMinutes += $clockOut->diffInMinutes($clockIn);
        }
        
        return round($totalMinutes / 60, 2);
    }

    /**
     * Get productive hours (like salary slip calculation: total_hours - idle_hours)
     */
    private function getProductiveHours($employeeId, $start, $end)
    {
        // Try to get from payroll table first (if available for the period)
        $month = $start->format('F Y');
        $payroll = Payroll::where('employee_id', $employeeId)
            ->where('month', $month)
            ->first();
        
        if ($payroll) {
            // Productive hours = total_hours - idle_hours (like salary slip)
            $totalHours = $payroll->total_hours ?? 0;
            $idleHours = $payroll->idle_hours ?? 0;
            $productiveHrs = max(0, $totalHours - $idleHours);
            
            if ($productiveHrs > 0) {
                return round($productiveHrs, 2);
            }
            
            // If productive_hrs is stored directly, use it
            if ($payroll->productive_hrs > 0) {
                return round($payroll->productive_hrs, 2);
            }
        }
        
        // Fallback to DAR total_time
        $dars = Dar::where('user_id', $employeeId)
            ->whereBetween('report_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get();
        
        return round($dars->sum('total_time'), 2);
    }

    /**
     * Get task completion metrics from Task table (status = Done)
     */
    private function getTaskCompletionMetrics($employeeEmail, $start, $end, $workspaceId)
    {
        // Get all tasks assigned to employee in the date range
        $tasks = Task::where('workspace', $workspaceId)
            ->where(function($query) use ($employeeEmail) {
                $query->where('assign_to', 'LIKE', "%{$employeeEmail}%")
                      ->orWhere('assign_to', $employeeEmail);
            })
            ->whereBetween('created_at', [$start, $end])
            ->get();
        
        $totalAssigned = $tasks->count();
        
        // Get completed tasks (status = "Done" from taskboard)
        $completedTasks = $tasks->filter(function($task) {
            return $task->status === 'Done' && $task->completion_date !== null;
        });
        $totalCompleted = $completedTasks->count();
        
        $durations = [];
        foreach ($completedTasks as $task) {
            if ($task->start_date && $task->completion_date) {
                $startDate = Carbon::parse($task->start_date);
                $completionDate = Carbon::parse($task->completion_date);
                $durations[] = [
                    'minutes' => $startDate->diffInMinutes($completionDate),
                    'days' => $startDate->diffInDays($completionDate),
                ];
            }
        }
        
        $avgDurationMinutes = count($durations) > 0 
            ? round(collect($durations)->avg('minutes'), 2) 
            : 0;
        $avgDurationDays = count($durations) > 0 
            ? round(collect($durations)->avg('days'), 2) 
            : 0;
        
        $completionRate = $totalAssigned > 0 
            ? round(($totalCompleted / $totalAssigned) * 100, 2) 
            : 0;
        
        return [
            'total_assigned' => $totalAssigned,
            'total_completed' => $totalCompleted,
            'completion_rate' => $completionRate,
            'avg_duration_minutes' => $avgDurationMinutes,
            'avg_duration_days' => $avgDurationDays,
        ];
    }

    /**
     * Calculate efficiency score based on ETC vs ATC
     */
    private function calculateEfficiencyScore($etc, $atc)
    {
        if ($etc == 0) return 0;
        $ratio = ($atc / $etc) * 100;
        // If ATC is close to ETC (within 10%), score is high
        // If ATC is much higher than ETC, score is lower
        if ($ratio <= 110) return 100;
        if ($ratio <= 120) return 90;
        if ($ratio <= 150) return 75;
        if ($ratio <= 200) return 50;
        return max(0, 100 - ($ratio - 200));
    }

    /**
     * Calculate productivity score
     */
    private function calculateProductivityScore($productiveHours, $totalWorkingHours)
    {
        if ($totalWorkingHours == 0) return 0;
        $ratio = ($productiveHours / $totalWorkingHours) * 100;
        return min(100, round($ratio, 2));
    }

    /**
     * Calculate task performance score
     */
    private function calculateTaskPerformanceScore($completionRate)
    {
        return min(100, round($completionRate, 2));
    }

    /**
     * Calculate timeliness score
     */
    private function calculateTimelinessScore($avgDays)
    {
        // Lower average days = better score
        // If avg days <= 1, score is 100
        // If avg days > 7, score decreases
        if ($avgDays <= 1) return 100;
        if ($avgDays <= 3) return 90;
        if ($avgDays <= 5) return 75;
        if ($avgDays <= 7) return 60;
        return max(0, 100 - (($avgDays - 7) * 5));
    }

    /**
     * Show employee performance report
     */
    public function showReport($id)
    {
        $performance = PerformanceManagement::with(['employee', 'feedbacks.givenBy'])
            ->findOrFail($id);
        
        // Check access
        $isPrivileged = Auth::user()->type === 'super admin' || 
            in_array(Auth::user()->email, ['president@5core.com', 'hr@5core.com', 'tech-support@5core.com', 'support@5core.com']);
        
        if (!$isPrivileged && $performance->employee_id != Auth::id()) {
            abort(403);
        }
        
        return view('performance.report', compact('performance', 'isPrivileged'));
    }

    /**
     * Store feedback from senior management
     */
    public function storeFeedback(Request $request)
    {
        // Check if user is privileged (only privileged users can add feedback)
        $privilegedEmails = [
            'president@5core.com',
            'hr@5core.com',
            'tech-support@5core.com',
            'support@5core.com',
            'mgr-advertisement@5core.com',
            'mgr-content@5core.com',
        ];
        
        $isPrivileged = Auth::user()->type === 'super admin' || 
            in_array(Auth::user()->email, $privilegedEmails);
        
        if (!$isPrivileged) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to add feedback. Only senior management can add feedback.'
            ], 403);
        }
        
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'performance_management_id' => 'nullable|exists:performance_management,id',
            'period' => 'required|string',
            'period_type' => 'required|in:monthly,weekly,quarterly,yearly',
            'feedback_date' => 'required|date',
            'communication_skill' => 'nullable|numeric|min:0|max:100',
            'teamwork' => 'nullable|numeric|min:0|max:100',
            'problem_solving' => 'nullable|numeric|min:0|max:100',
            'initiative' => 'nullable|numeric|min:0|max:100',
            'quality_of_work' => 'nullable|numeric|min:0|max:100',
            'reliability' => 'nullable|numeric|min:0|max:100',
            'adaptability' => 'nullable|numeric|min:0|max:100',
            'leadership' => 'nullable|numeric|min:0|max:100',
            'custom_parameters' => 'nullable|array',
        ]);
        
        $workspaceId = getActiveWorkSpace();
        
        $feedback = PerformanceFeedback::create([
            'employee_id' => $request->employee_id,
            'performance_management_id' => $request->performance_management_id,
            'given_by' => Auth::id(),
            'period_type' => $request->period_type,
            'period' => $request->period,
            'feedback_date' => $request->feedback_date,
            'communication_skill' => $request->communication_skill,
            'teamwork' => $request->teamwork,
            'problem_solving' => $request->problem_solving,
            'initiative' => $request->initiative,
            'quality_of_work' => $request->quality_of_work,
            'reliability' => $request->reliability,
            'adaptability' => $request->adaptability,
            'leadership' => $request->leadership,
            'custom_parameters' => $request->custom_parameters,
            'strengths' => $request->strengths,
            'areas_for_improvement' => $request->areas_for_improvement,
            'general_feedback' => $request->general_feedback,
            'goals' => $request->goals,
            'workspace_id' => $workspaceId,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Feedback saved successfully',
            'data' => $feedback
        ]);
    }

    /**
     * Get performance data for charts (API)
     */
    public function getChartData($id)
    {
        $performance = PerformanceManagement::with('feedbacks')->findOrFail($id);
        
        // Prepare chart data
        $chartData = [
            'scores' => [
                'labels' => ['Efficiency', 'Productivity', 'Task Performance', 'Timeliness'],
                'data' => [
                    $performance->efficiency_score,
                    $performance->productivity_score,
                    $performance->task_performance_score,
                    $performance->timeliness_score,
                ],
            ],
            'metrics' => [
                'labels' => ['ETC Hours', 'ATC Hours', 'Working Hours', 'Productive Hours'],
                'data' => [
                    $performance->etc_hours,
                    $performance->atc_hours,
                    $performance->total_working_hours,
                    $performance->productive_hours,
                ],
            ],
            'feedback' => [],
        ];
        
        // Add feedback data if available
        if ($performance->feedbacks->count() > 0) {
            $feedback = $performance->feedbacks->first();
            $chartData['feedback'] = [
                'labels' => ['Communication', 'Teamwork', 'Problem Solving', 'Initiative', 'Quality', 'Reliability', 'Adaptability', 'Leadership'],
                'data' => [
                    $feedback->communication_skill ?? 0,
                    $feedback->teamwork ?? 0,
                    $feedback->problem_solving ?? 0,
                    $feedback->initiative ?? 0,
                    $feedback->quality_of_work ?? 0,
                    $feedback->reliability ?? 0,
                    $feedback->adaptability ?? 0,
                    $feedback->leadership ?? 0,
                ],
            ];
        }
        
        return response()->json($chartData);
    }
}
