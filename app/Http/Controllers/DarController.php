<?php

namespace App\Http\Controllers;

use App\Models\Dar;
use App\Models\DarTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class DarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dars = Dar::with('tasks')->where('user_id', Auth::id())
                   ->orderBy('created_at', 'desc')
                   ->get();
        
        return view('dar.index', compact('dars'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_date' => 'required|date',
            'tasks' => 'required|array|min:1',
            'tasks.*.group_name' => 'required|string|max:100',
            'tasks.*.description' => 'required|string|max:255',
            'tasks.*.time_spent' => 'required|integer|min:1',
            'tasks.*.status' => 'required|in:Complete,Pending,In Progress'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

    // ...existing code...
        $totalTimeMinutes = array_sum(array_column($request->tasks, 'time_spent'));
        $totalTimeHours = $totalTimeMinutes / 60;

        try {
            DB::beginTransaction();

            // Check if DAR already exists for user and date
            $dar = Dar::where('user_id', Auth::id())
                ->where('report_date', $request->report_date)
                ->first();

            if ($dar) {
                // Update existing DAR
                $dar->total_time = $totalTimeHours;
                $dar->workspace_id = 1;
                $dar->created_by = Auth::id();
                $dar->save();

                // Delete old tasks
                $dar->tasks()->delete();
            } else {
                // Create new DAR
                $dar = Dar::create([
                    'user_id' => Auth::id(),
                    'report_date' => $request->report_date,
                    'total_time' => $totalTimeHours,
                    'workspace_id' => 1, // Set default workspace
                    'created_by' => Auth::id()
                ]);
            }

            // Create task records
            foreach ($request->tasks as $taskData) {
                DarTask::create([
                    'dar_id' => $dar->id,
                    'group_name' => $taskData['group_name'],
                    'description' => $taskData['description'],
                    'time_spent' => $taskData['time_spent'],
                    'status' => $taskData['status']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Daily Activity Report submitted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $dar = Dar::with('tasks', 'user')->findOrFail($id);
        
        // Check if user can view this DAR
        if ($dar->user_id !== Auth::id() && Auth::user()->type !== 'super admin') {
            abort(403);
        }
        
        return view('dar.show', compact('dar'));
    }

    /**
     * Display DAR reports page - Only for admin and specific users
     */
public function reports()
{
    // Check if user is super admin or has privileged email
    $privilegedEmails = [
        'president@5core.com',
        'tech-support@5core.com',
        'support@5core.com',
        'mgr-advertisement@5core.com',
        'mgr-content@5core.com',
    ];

    $isPrivileged = Auth::user()->type === 'super admin' || in_array(Auth::user()->email, $privilegedEmails);

    // Get employees list (all employees for privileged users, only themselves for regular users)
    if ($isPrivileged) {
        $employees = User::where('type', '!=', 'super admin')->get();
    } else {
        $employees = User::where('id', Auth::id())->get();
    }

    // Get DARs (all for privileged users, only their own for regular users)
    if ($isPrivileged) {
        $allReports = Dar::with(['tasks', 'user'])
            ->orderBy('report_date', 'desc')
            ->get();
    } else {
        $allReports = Dar::with(['tasks', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('report_date', 'desc')
            ->get();
    }

    // Find employees who filled/missed DARs
    $filledIds = $allReports->pluck('user_id')->unique()->toArray();
    $filledEmployees = $employees->whereIn('id', $filledIds);
    $missedEmployees = $employees->whereNotIn('id', $filledIds);

    return view('dar.reports', compact('employees', 'allReports', 'filledEmployees', 'missedEmployees'));
}
    /**
     * Get DAR report data based on date range and employee
     */
public function getReportData(Request $request)
{
    $validator = Validator::make($request->all(), [
        'start_date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'employee_id' => 'required|exists:users,id'
    ], [
        'end_date.after_or_equal' => 'End date must be on or after the start date.'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Check if user is privileged (super admin or specific email)
    $privilegedEmails = [
        'president@5core.com',
        'tech-support@5core.com',
        'support@5core.com',
        'mgr-advertisement@5core.com',
        'mgr-content@5core.com',
    ];

    $isPrivileged = Auth::user()->type === 'super admin' || in_array(Auth::user()->email, $privilegedEmails);

    // If not privileged, restrict to their own reports
    if (!$isPrivileged && $request->employee_id != Auth::id()) {
        return response()->json(['error' => 'Unauthorized access'], 403);
    }

    try {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        // Add debugging
        \Log::info('=== DAR DEBUG ===');
        \Log::info('Request All: ' . json_encode($request->all()));
        \Log::info('Start Date: ' . $startDate);
        \Log::info('End Date Raw: ' . ($endDate ?? 'NULL'));
        \Log::info('End Date Type: ' . gettype($endDate));
        \Log::info('End Date Length: ' . strlen($endDate ?? ''));
        \Log::info('Has End Date: ' . ($request->has('end_date') ? 'TRUE' : 'FALSE'));
        \Log::info('End Date Empty: ' . (empty($endDate) ? 'TRUE' : 'FALSE'));
        \Log::info('End Date Trim Empty: ' . (empty(trim($endDate ?? '')) ? 'TRUE' : 'FALSE'));
        
        // Check if this is a date range query - handle empty string properly
        $hasEndDate = !empty($endDate) && trim($endDate) !== '';
        $isRange = $hasEndDate && $endDate !== $startDate;
        
        \Log::info('Has End Date: ' . ($hasEndDate ? 'TRUE' : 'FALSE'));
        \Log::info('Is Range: ' . ($isRange ? 'TRUE' : 'FALSE'));
        
        // If no end date provided, use start date for single date query
        if (!$hasEndDate) {
            $endDate = $startDate;
        }
        
        // If same dates provided, it's a single date query but we keep the end date
        if ($hasEndDate && !$isRange) {
            \Log::info('Same dates provided - treating as single date query');
        }
        
        \Log::info('Final Start Date: ' . $startDate);
        \Log::info('Final End Date: ' . $endDate);

        $darsQuery = Dar::with(['tasks', 'user'])
            ->where('user_id', $request->employee_id)
            ->where('report_date', '>=', $startDate)
            ->where('report_date', '<=', $endDate)
            ->orderBy('report_date', 'asc');

        $dars = $darsQuery->get();

        $employeeName = User::find($request->employee_id)->name ?? 'Unknown';

        if ($dars->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No DAR found for selected date range and employee.',
                'data' => [
                    'dars' => [],
                    'tasks' => [],
                    'total_time_formatted' => '0h 0m',
                    'employee_name' => $employeeName,
                    'date_range' => $isRange ? $startDate . ' to ' . $endDate : $startDate,
                    'is_range' => $isRange
                ]
            ]);
        }

        // Calculate total time across all DARs in the range
        $totalTimeHours = $dars->sum('total_time');
        $totalTimeMinutes = $totalTimeHours * 60;

        // Collect all tasks from all DARs
        $allTasks = [];
        foreach ($dars as $dar) {
            foreach ($dar->tasks as $task) {
                $task->report_date = $dar->report_date; // Add report date to each task
                $allTasks[] = $task;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'dars' => $dars,
                'tasks' => $allTasks,
                'total_time_formatted' => $this->formatTime($totalTimeMinutes),
                'employee_name' => $dars->first()->user->name,
                'date_range' => $isRange ? $startDate . ' to ' . $endDate : $startDate,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_range' => $isRange
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('DAR Report Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error fetching report data: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Format time in minutes to hours and minutes
     */
    private function formatTime($totalMinutes)
    {
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return "{$hours}h {$minutes}m";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $dar = Dar::findOrFail($id);
            
            // Check if user can delete this DAR
            if ($dar->user_id !== Auth::id() && Auth::user()->type !== 'super admin') {
                return response()->json(['error' => __('Permission denied.')], 403);
            }

            $dar->delete();
            
            return response()->json(['success' => __('DAR deleted successfully.')]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('Something went wrong.')], 500);
        }
    }
    /**
     * AJAX: Get filled/missed employees for a given date
     */
    public function reportsSummary(Request $request)
    {
        $date = $request->input('report_date', date('Y-m-d'));
        $employees = User::where('type', '!=', 'super admin')->get();
        $dars = Dar::where('report_date', $date)->get();
        $filledIds = $dars->pluck('user_id')->unique()->toArray();
        $filled = $employees->whereIn('id', $filledIds)->values()->all();
        $missed = $employees->whereNotIn('id', $filledIds)->values()->all();
        return response()->json([
            'filled' => array_map(function($e){ return ['id'=>$e->id,'name'=>$e->name]; }, $filled),
            'missed' => array_map(function($e){ return ['id'=>$e->id,'name'=>$e->name]; }, $missed)
        ]);
    }
}
