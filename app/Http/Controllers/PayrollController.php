<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Workdo\Taskly\Entities\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExport;
use Symfony\Component\Mailer\Exception\TransportException;

class PayrollController extends Controller
{
public function index(Request $request) 
{
    try {
        // Increase execution time to handle TeamLogger API calls
        set_time_limit(300); // 5 minutes
        ini_set('max_execution_time', 300);
        
        // Middleware already ensures admin access, so no need to check again
        
        // Get active workspace ID - fallback to session or Auth user's workspace
        $workspaceId = getActiveWorkSpace();
        
        // Get selected month from request or default to current month
        // $selectedMonth = $request->get('month', 'August 2025');
        $selectedMonth = $request->get('month', Carbon::now()->format('F Y'));

        // AUTO-COPY: If selected month is current month, copy bank details from previous month
        $currentMonth = Carbon::now()->format('F Y');
        if ($selectedMonth === $currentMonth) {
            $this->autoCopyBankDetailsFromPreviousMonth($workspaceId, $selectedMonth);
        }
        
        // Get all employees from the workspace
        $employees = User::where('workspace_id', $workspaceId)
            ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->select(
                'users.id as employee_id',
                'users.name',
                'users.email as email_address',
                'departments.name as department'
            )
            ->whereNotNull('employees.user_id') // Only get users who are employees
            ->orderBy('users.name', 'asc')
            ->get();
        
        // Get all payroll records for this workspace for the selected month
        $payrollRecords = Payroll::where('workspace_id', $workspaceId)
            ->where('month', $selectedMonth)
            ->get()
            ->groupBy('employee_id');
        
        // Get previous month name for sal_previous calculation
        $previousMonth = $this->getPreviousMonth($selectedMonth);
        
        // Get previous month payroll records for sal_previous calculation
        $previousMonthRecords = Payroll::where('workspace_id', $workspaceId)
            ->where('month', $previousMonth)
            ->get()
            ->groupBy('employee_id');
        
        // Log debugging information - remove in production
        \Log::info("Selected Month: $selectedMonth, Previous Month: $previousMonth");
        \Log::info("Previous Month Records: " . json_encode($previousMonthRecords->keys()));
        
        // Get all archived employees (from any month) to exclude from main payroll list
        $archivedEmployeeIds = Payroll::where('workspace_id', $workspaceId)
            ->where(function($query) {
                $query->where('is_enabled', false)
                      ->orWhere('is_contractual', true);
            })
            ->pluck('employee_id')
            ->unique()
            ->toArray();
        
        // OPTIMIZATION: Fetch TeamLogger data ONCE for all employees instead of individual calls
        $allTeamLoggerData = $this->getAllTeamLoggerDataBatch($selectedMonth);
        
        // Filter employees: only show those who are NOT archived/contractual and have ENABLED payroll records or no payroll records at all for selected month
        $payrolls = $employees->filter(function($employee) use ($payrollRecords, $selectedMonth, $archivedEmployeeIds) {
            // Exclude archived/contractual employees from main payroll list
            if (in_array($employee->employee_id, $archivedEmployeeIds)) {
                return false;
            }
            
            $employeePayrolls = $payrollRecords->get($employee->employee_id, collect());
            
            // Find payroll for selected month
            $currentMonthPayroll = $employeePayrolls->first();
            
            // Only include employee if they have no payroll record OR have an enabled payroll record for this month
            return !$currentMonthPayroll || ($currentMonthPayroll && $currentMonthPayroll->is_enabled);
        })->map(function($employee) use ($payrollRecords, $previousMonthRecords, $selectedMonth, $previousMonth, $allTeamLoggerData) {
            $employeePayrolls = $payrollRecords->get($employee->employee_id, collect());
            $employeePreviousPayrolls = $previousMonthRecords->get($employee->employee_id, collect());
            
            // Find payroll for selected month
            $currentMonthPayroll = $employeePayrolls->first();
            
            // Find payroll for previous month (for sal_previous)
            $previousMonthPayroll = $employeePreviousPayrolls->first();
            
            // Get ETC and ATC data for the employee (still individual but faster)
            $etcAtcData = $this->getEmployeeETCAndATC($employee->email_address, $selectedMonth);
            
            // Get TeamLogger data from batch result instead of individual API call
            $emailKey = strtolower(trim($employee->email_address));
            $teamLoggerData = $allTeamLoggerData[$emailKey] ?? ['hours' => 0, 'total_hours' => 0, 'idle_hours' => 0];
            
            // Get overdue count for the employee
            $overdueCount = $this->getEmployeeOverdueCount($employee->email_address, $selectedMonth);
            
            if ($currentMonthPayroll) {
                // Employee has payroll record for selected month
                $approvedHrs = $currentMonthPayroll->approved_hrs ?? $teamLoggerData['hours'];
                $approvalStatus = $currentMonthPayroll->approval_status ?? 'pending';
                
                // Calculate payable based on approval status and approved hours
                $payable = 0;
                $totalPayable = 0;
                
                if ($approvalStatus === 'approved' && $approvedHrs > 0 && $currentMonthPayroll->salary_current) {
                    // Payable = (Salary × Hours / 200) - incentive is NOT included
                    $payable = ($currentMonthPayroll->salary_current * $approvedHrs / 200);
                    // Total Payable = Payable + Incentive - Advance + Extra
                    $totalPayable = $payable + ($currentMonthPayroll->incentive ?? 0) - ($currentMonthPayroll->advance ?? 0) + ($currentMonthPayroll->extra ?? 0);
                }
                
                // Use stored sal_previous from database - do NOT dynamically calculate from other months
                // This prevents cross-month data contamination
                $salPrevious = $currentMonthPayroll->sal_previous;
                
                return (object) [
                    'employee_id' => $employee->employee_id,
                    'name' => $employee->name,
                    'email_address' => $employee->email_address,
                    'department' => $employee->department,
                    'id' => $currentMonthPayroll->id,
                    'month' => $currentMonthPayroll->month,
                    'sal_previous' => $salPrevious,
                    'increment' => $currentMonthPayroll->increment,
                    'salary_current' => $currentMonthPayroll->salary_current,
                    'productive_hrs' => $teamLoggerData['hours'], // Use TeamLogger data
                    'approved_hrs' => $approvedHrs,
                    'approval_status' => $approvalStatus,
                    'total_hours' => $teamLoggerData['total_hours'] ?? 0,
                    'idle_hours' => $teamLoggerData['idle_hours'] ?? 0,
                    'etc_hours' => $etcAtcData['etc'],
                    'atc_hours' => $etcAtcData['atc'],
                    'overdue_count' => $overdueCount,
                    'incentive' => $currentMonthPayroll->incentive,
                    'payable' => round($payable),
                    'advance' => $currentMonthPayroll->advance,
                    'extra' => $currentMonthPayroll->extra,
                    'total_payable' => round($totalPayable),
                    'bank1' => $currentMonthPayroll->bank1,
                    'bank2' => $currentMonthPayroll->bank2,
                    'up' => $currentMonthPayroll->up,
                    'payment_done' => $currentMonthPayroll->payment_done,
                    'is_enabled' => $currentMonthPayroll->is_enabled ?? true,
                    'created_at' => $currentMonthPayroll->created_at
                ];
            } else {
                // Employee doesn't have payroll record for selected month
                // Get salary data from previous month to auto-populate in current month
                $salPrevious = null;
                $salaryCurrent = null;
                
                // Find payroll for previous month to get salary data
                if ($previousMonthPayroll) {
                    $salPrevious = $previousMonthPayroll->salary_current ?? 0;
                    $salaryCurrent = $salPrevious; // Set current salary same as previous month's salary
                }
                
                return (object) [
                    'employee_id' => $employee->employee_id,
                    'name' => $employee->name,
                    'email_address' => $employee->email_address,
                    'department' => $employee->department,
                    'id' => null,
                    'month' => $selectedMonth,
                    'sal_previous' => $salPrevious,
                    'increment' => 0, // Default increment to 0
                    'salary_current' => $salaryCurrent,
                    'productive_hrs' => $teamLoggerData['hours'], // Use TeamLogger data
                    'approved_hrs' => $teamLoggerData['hours'], // Default to TeamLogger hours
                    'approval_status' => 'pending', // Default status
                    'total_hours' => $teamLoggerData['total_hours'] ?? 0,
                    'idle_hours' => $teamLoggerData['idle_hours'] ?? 0,
                    'etc_hours' => $etcAtcData['etc'],
                    'atc_hours' => $etcAtcData['atc'],
                    'overdue_count' => $overdueCount,
                    'incentive' => null,
                    'payable' => 0, // Default to 0 since no approval and salary data
                    'advance' => null,
                    'extra' => null,
                    'total_payable' => 0, // Default to 0
                    'bank1' => null,
                    'bank2' => null,
                    'up' => null,
                    'payment_done' => false,
                    'is_enabled' => true, // Default to enabled for new records
                    'created_at' => null
                ];
            }
        });
        
        return view('payroll.payroll', [
            'payrolls' => $payrolls, 
            'selectedMonth' => $selectedMonth
        ]);
    } catch (\Exception $e) {
        \Log::error('PayrollController index error: ' . $e->getMessage());
        
        // If there's an error, still show all employees with empty payroll data
        $employees = User::where('workspace_id', session('workspace_id') ?? Auth::user()->workspace_id ?? 1)
            ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->select(
                'users.id as employee_id',
                'users.name',
                'users.email as email_address',
                'departments.name as department'
            )
            ->whereNotNull('employees.user_id')
            ->orderBy('users.name', 'asc')
            ->get();
            
        return view('payroll.payroll', ['payrolls' => $employees]);
    }
}
    
    private function getPreviousMonth($currentMonth)
    {
        $months = [
            'January 2025', 'February 2025', 'March 2025', 'April 2025',
            'May 2025', 'June 2025', 'July 2025', 'August 2025',
            'September 2025', 'October 2025', 'November 2025', 'December 2025'
        ];
        
        $currentIndex = array_search($currentMonth, $months);
        
        if ($currentIndex === false || $currentIndex === 0) {
            return 'December 2024'; // Previous year December
        }
        
        return $months[$currentIndex - 1];
    }
    
    /**
     * Auto-copy bank details and UPI from previous month to current month
     * Only runs when selected month equals TODAY'S current month
     * Only copies if current month record doesn't already have bank details
     */
    private function autoCopyBankDetailsFromPreviousMonth($workspaceId, $currentMonth)
    {
        try {
            // Get today's actual current month
            $todayMonth = Carbon::now()->format('F Y');
            
            // Only auto-copy if the selected month is the same as today's month
            if ($currentMonth !== $todayMonth) {
                \Log::info("Auto-copy skipped: Selected month ({$currentMonth}) is not today's current month ({$todayMonth})");
                return;
            }
            
            // Get previous month
            $previousMonth = $this->getPreviousMonth($currentMonth);
            
            // Get all current month records
            $currentMonthRecords = Payroll::where('workspace_id', $workspaceId)
                ->where('month', $currentMonth)
                ->get();
            
            // Get all previous month records
            $previousMonthRecords = Payroll::where('workspace_id', $workspaceId)
                ->where('month', $previousMonth)
                ->where('is_enabled', true)
                ->get()
                ->keyBy('employee_id');
            
            if ($previousMonthRecords->isEmpty()) {
                \Log::info("Auto-copy skipped: No previous month records found for {$previousMonth}");
                return;
            }
            
            $updatedCount = 0;
            $createdCount = 0;
            
            // Get all employees
            $employees = User::where('workspace_id', $workspaceId)
                ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
                ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                ->select(
                    'users.id as employee_id',
                    'users.name',
                    'users.email as email_address',
                    'departments.name as department'
                )
                ->whereNotNull('employees.user_id')
                ->get()
                ->keyBy('employee_id');
            
            // Process each employee from previous month
            foreach ($previousMonthRecords as $employeeId => $prevRecord) {
                // Check if current month record exists
                $currentRecord = $currentMonthRecords->firstWhere('employee_id', $employeeId);
                
                if ($currentRecord) {
                    // Update existing record only if bank details are missing
                    if (empty($currentRecord->bank1) && empty($currentRecord->bank2) && empty($currentRecord->up)) {
                        $currentRecord->bank1 = $prevRecord->bank1;
                        $currentRecord->bank2 = $prevRecord->bank2;
                        $currentRecord->up = $prevRecord->up;
                        $currentRecord->save();
                        $updatedCount++;
                    }
                } else {
                    // Create new record with bank details only
                    $employee = $employees->get($employeeId);
                    if (!$employee) continue;
                    
                    Payroll::create([
                        'workspace_id' => $workspaceId,
                        'employee_id' => $employeeId,
                        'name' => $employee->name,
                        'email_address' => $employee->email_address,
                        'department' => $employee->department ?? 'N/A',
                        'month' => $currentMonth,
                        'bank1' => $prevRecord->bank1,
                        'bank2' => $prevRecord->bank2,
                        'up' => $prevRecord->up,
                        'sal_previous' => 0,
                        'increment' => 0,
                        'salary_current' => 0,
                        'productive_hrs' => 0,
                        'approved_hrs' => 0,
                        'approval_status' => 'pending',
                        'total_hours' => 0,
                        'idle_hours' => 0,
                        'etc_hours' => 0,
                        'atc_hours' => 0,
                        'overdue_count' => 0,
                        'incentive' => 0,
                        'payable' => 0,
                        'advance' => 0,
                        'extra' => 0,
                        'total_payable' => 0,
                        'payment_done' => false,
                        'is_enabled' => true,
                        'is_contractual' => false,
                        'created_by' => Auth::id()
                    ]);
                    $createdCount++;
                }
            }
            
            if ($updatedCount > 0 || $createdCount > 0) {
                \Log::info("Auto-copied bank details for TODAY'S month: Updated {$updatedCount}, Created {$createdCount} records from {$previousMonth} to {$currentMonth}");
            }
            
        } catch (\Exception $e) {
            \Log::error("Auto-copy bank details failed: " . $e->getMessage());
        }
    }
    
    public function getEmployees()
    {
        try {
            // Get active workspace ID
            $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
            
            // Get employees from the HRM system
            $employees = User::where('workspace_id', $workspaceId)
                ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
                ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'departments.name as department_name'
                )
                ->whereNotNull('employees.user_id') // Only get users who are employees
                ->get();
            
            return response()->json([
                'success' => true,
                'employees' => $employees
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching employees: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:users,id',
                'name' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'email_address' => 'required|email|max:255',
                'month' => 'required|string|max:50',
                'sal_previous' => 'nullable|numeric|min:0',
                'increment' => 'nullable|numeric|min:0',
                'productive_hrs' => 'nullable|integer|min:0',
                'incentive' => 'nullable|numeric|min:0',
                'advance' => 'nullable|numeric|min:0',
                'payment_done' => 'nullable|boolean',
            ]);
            
            // Get workspace and creator IDs
            $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
            $createdBy = Auth::id();
            
            // Calculate derived values
            // Always get previous month's salary data for new records, but use request values if provided
            $previousMonth = $this->getPreviousMonth($request->month);
            $previousMonthPayroll = Payroll::where('employee_id', $request->employee_id)
                ->where('workspace_id', $workspaceId)
                ->where('month', $previousMonth)
                ->first();
            
            // Use request value if provided, otherwise get from previous month
            $salPrevious = $request->sal_previous;
            if (($salPrevious === null || $salPrevious == 0) && $previousMonthPayroll) {
                $salPrevious = $previousMonthPayroll->salary_current ?? 0;
            }
            
            $increment = $request->increment ?? 0;
            $salaryCurrent = $request->salary_current ?? ($salPrevious + $increment);
            $productiveHrs = $request->productive_hrs ?? 0;
            $incentive = $request->incentive ?? 0;
            $advance = $request->advance ?? 0;
            
            // Use productive hours as approved hours by default
            // Don't automatically change approval status - let user decide
            $approvedHrs = $request->approved_hrs ?? $productiveHrs; // Use actual productive hours if not provided
            $approvalStatus = $request->approval_status ?? 'pending'; // Use provided status or default to pending
            
            // Calculate payable: (Current Salary * Approved Hours / 200) - incentive is NOT included
            $payable = ($salaryCurrent * $approvedHrs / 200);
            // Total Payable = Payable + Incentive - Advance + Extra
            $totalPayable = $payable + $incentive - $advance;
            
            // Get ETC and ATC data for the employee
            $etcAtcData = $this->getEmployeeETCAndATC($request->email_address, $request->month);
            
            // Get TeamLogger data for the employee
            $teamLoggerData = $this->getEmployeeTeamLoggerData($request->email_address, $request->month);
            
            // Check if payroll already exists for this employee AND MONTH
            $existingPayroll = Payroll::where('employee_id', $request->employee_id)
                ->where('workspace_id', $workspaceId)
                ->where('month', $request->month)
                ->first();
            
            if ($existingPayroll) {
                // Update existing record
                $existingPayroll->update([
                    'name' => $request->name,
                    'department' => $request->department,
                    'email_address' => $request->email_address,
                    'month' => $request->month,
                    'sal_previous' => $salPrevious,
                    'increment' => $increment,
                    'salary_current' => $salaryCurrent,
                    'productive_hrs' => $teamLoggerData['hours'], // Use TeamLogger data instead of request
                    'approved_hrs' => $approvedHrs, // Set approved hours
                    'approval_status' => $approvalStatus, // Set approval status
                    'etc_hours' => $etcAtcData['etc'],
                    'atc_hours' => $etcAtcData['atc'],
                    'incentive' => $incentive,
                    'payable' => $payable,
                    'advance' => $advance,
                    'extra' => $request->extra ?? 0,
                    'total_payable' => $totalPayable,
                    'bank1' => $request->bank1 ?? ($previousMonthPayroll ? $previousMonthPayroll->bank1 : null),
                    'bank2' => $request->bank2 ?? ($previousMonthPayroll ? $previousMonthPayroll->bank2 : null),
                    'up' => $request->up ?? ($previousMonthPayroll ? $previousMonthPayroll->up : null),
                    'payment_done' => $request->payment_done ?? false,
                ]);
                $payroll = $existingPayroll;
            } else {
                // Check if we should copy bank details from previous month
                $bank1 = $request->bank1;
                $bank2 = $request->bank2;
                $up = $request->up;
                
                // If bank details are not provided in the request, try to get them from previous month
                if (($bank1 === null || $bank2 === null || $up === null) && $previousMonthPayroll) {
                    // Copy bank details from previous month's record if they exist
                    $bank1 = $bank1 ?? $previousMonthPayroll->bank1;
                    $bank2 = $bank2 ?? $previousMonthPayroll->bank2;
                    $up = $up ?? $previousMonthPayroll->up;
                }
                
                // Create new record
                $payroll = Payroll::create([
                    'employee_id' => $request->employee_id,
                    'name' => $request->name,
                    'department' => $request->department,
                    'email_address' => $request->email_address,
                    'month' => $request->month,
                    'sal_previous' => $salPrevious,
                    'increment' => $increment,
                    'salary_current' => $salaryCurrent,
                    'productive_hrs' => $teamLoggerData['hours'], // Use TeamLogger data instead of request
                    'approved_hrs' => $approvedHrs, // Set approved hours
                    'approval_status' => $approvalStatus, // Set approval status
                    'etc_hours' => $etcAtcData['etc'],
                    'atc_hours' => $etcAtcData['atc'],
                    'incentive' => $incentive,
                    'payable' => $payable,
                    'advance' => $advance,
                    'extra' => $request->extra ?? 0,
                    'total_payable' => $totalPayable,
                    'bank1' => $bank1, // Use either request value or copied value from previous month
                    'bank2' => $bank2, // Use either request value or copied value from previous month
                    'up' => $up, // Use either request value or copied value from previous month
                    'payment_done' => $request->payment_done ?? false,
                    'is_enabled' => true, // Default to enabled
                    'workspace_id' => $workspaceId,
                    'created_by' => $createdBy,
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Payroll entry saved successfully',
                'data' => [
                    'id' => $payroll->id,
                    'employee_id' => $payroll->employee_id,
                    'name' => $payroll->name,
                    'department' => $payroll->department,
                    'email_address' => $payroll->email_address,
                    'month' => $payroll->month,
                    'sal_previous' => $payroll->sal_previous,
                    'increment' => $payroll->increment,
                    'salary_current' => $payroll->salary_current,
                    'productive_hrs' => $payroll->productive_hrs,
                    'approved_hrs' => $payroll->approved_hrs,
                    'approval_status' => $payroll->approval_status,
                    'etc_hours' => $payroll->etc_hours,
                    'atc_hours' => $payroll->atc_hours,
                    'incentive' => $payroll->incentive,
                    'payable' => $payroll->payable,
                    'advance' => $payroll->advance,
                    'total_payable' => $payroll->total_payable,
                    'payment_done' => $payroll->payment_done,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating payroll entry: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
            
            // Try to find the payroll record first
            $payroll = Payroll::where('workspace_id', $workspaceId)->find($id);
            
            // If no payroll record exists, create a new one
            if (!$payroll) {
                // Treat this as a new record creation
                $request->validate([
                    'employee_id' => 'required|exists:users,id',
                    'name' => 'required|string|max:255',
                    'department' => 'required|string|max:255',
                    'email_address' => 'required|email|max:255',
                ]);
                
                return $this->store($request);
            }
            
            // Update existing record
            $request->validate([
                'name' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'email_address' => 'required|email|max:255',
                'month' => 'required|string|max:50',
                'sal_previous' => 'nullable|numeric|min:0',
                'increment' => 'nullable|numeric|min:0',
                'productive_hrs' => 'nullable|integer|min:0',
                'incentive' => 'nullable|numeric|min:0',
                'advance' => 'nullable|numeric|min:0',
                'payment_done' => 'nullable|boolean',
            ]);
            
            // Calculate derived values
            $salPrevious = $request->sal_previous;
            
            // Get previous month's data for reference
            $previousMonth = $this->getPreviousMonth($request->month);
            $previousMonthPayroll = Payroll::where('employee_id', $payroll->employee_id)
                ->where('workspace_id', $workspaceId)
                ->where('month', $previousMonth)
                ->first();
            
            // Try to get salary from previous month when sal_previous is missing
            if (!$salPrevious || $salPrevious == 0) {
                $salPrevious = $previousMonthPayroll ? $previousMonthPayroll->salary_current : 0;
            }
            
            // If bank details are not provided, get from previous month's record
            if (!$request->has('bank1') || !$request->bank1) {
                $request->merge(['bank1' => $previousMonthPayroll ? $previousMonthPayroll->bank1 : null]);
            }
            
            if (!$request->has('bank2') || !$request->bank2) {
                $request->merge(['bank2' => $previousMonthPayroll ? $previousMonthPayroll->bank2 : null]);
            }
            
            if (!$request->has('up') || !$request->up) {
                $request->merge(['up' => $previousMonthPayroll ? $previousMonthPayroll->up : null]);
            }
            
            $increment = $request->increment ?? 0;
            $salaryCurrent = $salPrevious + $increment;
            $incentive = $request->incentive ?? 0;
            $advance = $request->advance ?? 0;
            
            // Get current approved hours and approval status for payable calculation
            // IMPORTANT: Only use request values if they're explicitly different from stored values
            // This prevents accidental approval data changes when updating salary/increment
            $storedApprovedHrs = $payroll->approved_hrs ?? 0;
            $storedApprovalStatus = $payroll->approval_status ?? 'pending';
            
            // Don't auto-approve when increment is added - let user manually set approval
            $approvedHrs = $storedApprovedHrs;
            $approvalStatus = $storedApprovalStatus;
            
            // Only update approval data if request explicitly provides different values
            if ($request->has('approved_hrs') && $request->approved_hrs != $storedApprovedHrs) {
                $approvedHrs = $request->approved_hrs;
            } elseif (!$storedApprovedHrs || $storedApprovedHrs == 0) {
                // If no approved hours are set, use TeamLogger data as fallback
                $teamLoggerData = $this->getEmployeeTeamLoggerData($payroll->email_address, $request->month);
                $approvedHrs = $teamLoggerData['hours'];
            }
            
            if ($request->has('approval_status') && $request->approval_status != $storedApprovalStatus) {
                $approvalStatus = $request->approval_status;
            }
            
            // Calculate payable based on approval status and approved hours
            // Payable = (Salary × Hours / 200) - incentive is NOT included in payable
            $payable = 0;
            if ($approvalStatus === 'approved' && $approvedHrs > 0 && $salaryCurrent > 0) {
                $payable = ($salaryCurrent * $approvedHrs / 200);
            }
            
            $extra = $request->extra ?? $payroll->extra ?? 0;
            // Total Payable = Payable + Incentive - Advance + Extra
            $totalPayable = $payable + $incentive - $advance + $extra;
            
            // IMPORTANT: Only update TeamLogger data if explicitly requested
            // This preserves existing ETC/ATC data when just updating salary/increment
            $updateData = [
                'name' => $request->name,
                'department' => $request->department,
                'email_address' => $request->email_address,
                'month' => $request->month,
                'sal_previous' => $salPrevious,
                'increment' => $increment,
                'salary_current' => $salaryCurrent,
                'approved_hrs' => $approvedHrs, // Always update approved hours
                'approval_status' => $approvalStatus, // Always update approval status
                'incentive' => $incentive,
                'payable' => $payable,
                'advance' => $advance,
                'extra' => $extra,
                'total_payable' => $totalPayable,
                'bank1' => $request->bank1 ?? ($previousMonthPayroll ? $previousMonthPayroll->bank1 : $payroll->bank1),
                'bank2' => $request->bank2 ?? ($previousMonthPayroll ? $previousMonthPayroll->bank2 : $payroll->bank2),
                'up' => $request->up ?? ($previousMonthPayroll ? $previousMonthPayroll->up : $payroll->up),
                'payment_done' => $request->payment_done ?? false,
            ];
            
            // Remove the individual field updates since we're always updating these now
            // The logic above already handles when to change approved_hrs and approval_status
            
            // Only fetch and update TeamLogger data if explicitly requested OR if it's a new record with no existing data
            // This preserves ETC/ATC data when updating salary/increment via modal
            $shouldUpdateTeamLoggerData = $request->has('refresh_teamlogger_data') || 
                                         !$payroll->productive_hrs || 
                                         !$payroll->etc_hours || 
                                         !$payroll->atc_hours ||
                                         $payroll->month !== $request->month; // If month changed, get new data
            
            if ($shouldUpdateTeamLoggerData) {
                // Get ETC and ATC data for the employee
                $etcAtcData = $this->getEmployeeETCAndATC($payroll->email_address, $request->month);
                
                // Get TeamLogger data for the employee  
                $teamLoggerData = $this->getEmployeeTeamLoggerData($payroll->email_address, $request->month);
                
                $updateData['productive_hrs'] = $teamLoggerData['hours'];
                $updateData['etc_hours'] = $etcAtcData['etc'];
                $updateData['atc_hours'] = $etcAtcData['atc'];
                
                // Also update approved hours to match productive hours if not already set
                if (!$payroll->approved_hrs) {
                    $updateData['approved_hrs'] = $teamLoggerData['hours'];
                }
            }
            
            $payroll->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Payroll entry updated successfully',
                'data' => [
                    'id' => $payroll->id,
                    'employee_id' => $payroll->employee_id,
                    'name' => $payroll->name,
                    'department' => $payroll->department,
                    'email_address' => $payroll->email_address,
                    'month' => $payroll->month,
                    'sal_previous' => $payroll->sal_previous,
                    'increment' => $payroll->increment,
                    'salary_current' => $payroll->salary_current,
                    'productive_hrs' => $payroll->productive_hrs,
                    'approved_hrs' => $payroll->approved_hrs,
                    'approval_status' => $payroll->approval_status,
                    'incentive' => $payroll->incentive,
                    'payable' => $payroll->payable,
                    'advance' => $payroll->advance,
                    'total_payable' => $payroll->total_payable,
                    'payment_done' => $payroll->payment_done,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating payroll entry: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
            $payroll = Payroll::where('workspace_id', $workspaceId)->findOrFail($id);
            $payroll->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Payroll entry deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting payroll entry: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function markAsDone($id)
    {
        try {
            $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
            $payroll = Payroll::where('workspace_id', $workspaceId)->findOrFail($id);
            $payroll->update(['payment_done' => true]);
            
            // Automatically send salary slip email after marking as done
            $emailResult = $this->sendSalarySlipEmailAuto($payroll);
            
            $responseMessage = 'Payment marked as done successfully';
            if ($emailResult['success']) {
                $responseMessage .= ' and salary slip sent to ' . $payroll->email_address;
            } else {
                $responseMessage .= '. However, email sending failed: ' . $emailResult['message'];
            }
            
            return response()->json([
                'success' => true,
                'message' => $responseMessage,
                'data' => $payroll,
                'email_sent' => $emailResult['success']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking payment as done: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Automatically send salary slip email when payment is marked as done
     * This is a helper method that doesn't require request validation
     */
    private function sendSalarySlipEmailAuto($payroll)
    {
        \Mail::raw('SMTP test from Laravel', function($msg){
    $msg->to('software13@5core.com')->subject('SMTP Test');
});
        try {
            // Validate required data exists
            if (!$payroll->email_address || !$payroll->name || !$payroll->month) {
                return [
                    'success' => false,
                    'message' => 'Missing required employee data (email, name, or month)'
                ];
            }
            
            $employeeName = $payroll->name;
            $employeeEmail = $payroll->email_address;
            $month = $payroll->month;
            
            // Debug mail configuration
            Log::info('Auto Email - Mail Configuration Debug', [
                'employee_email' => $employeeEmail,
                'employee_name' => $employeeName,
                'month' => $month
            ]);
            
            // Create email subject and body using the template
            $subject = "Salary Slip for {$month}";
            
            $emailBody = "Dear {$employeeName},\n\n";
            $emailBody .= "Please find attached your salary slip for {$month}.\n";
            $emailBody .= "If you have any questions or notice any discrepancies, feel free to reach out to the HR/Accounts department.\n\n";
            $emailBody .= "Thank you for your continued contributions to the company.\n\n";
            $emailBody .= "Best regards,\n";
            $emailBody .= "HR/Accounts Department\n";
            $emailBody .= "5CORE INC";
            
            // Generate PDF for attachment
            $pdfPath = $this->generateSalarySlipPDF($payroll);
            
            $emailSent = false;
            $errorMessage = '';
            
            // Send email
            try {
                // Configure mail settings dynamically
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.transport' => 'smtp',
                    'mail.mailers.smtp.host' => env('MAIL_HOST', '5coremanagement.com'),
                    'mail.mailers.smtp.port' => env('MAIL_PORT', 465),
                    'mail.mailers.smtp.encryption' => env('MAIL_ENCRYPTION', 'ssl'),
                    'mail.mailers.smtp.username' => env('MAIL_USERNAME'),
                    'mail.mailers.smtp.password' => env('MAIL_PASSWORD'),
                    'mail.mailers.smtp.timeout' => 60,
                    'mail.mailers.smtp.verify_peer' => false,
                    'mail.mailers.smtp.verify_peer_name' => false,
                    'mail.from.address' => env('MAIL_FROM_ADDRESS'),
                    'mail.from.name' => env('MAIL_FROM_NAME', '5 Core Management')
                ]);

                Mail::raw($emailBody, function ($message) use ($employeeEmail, $subject, $pdfPath, $month) {
                    $message->to($employeeEmail)
                            ->subject($subject);
                    
                    // Attach PDF if it exists
                    if ($pdfPath && file_exists($pdfPath)) {
                        $fileExtension = pathinfo($pdfPath, PATHINFO_EXTENSION);
                        
                        if ($fileExtension === 'pdf') {
                            $message->attach($pdfPath, [
                                'as' => "salary_slip_{$month}.pdf",
                                'mime' => 'application/pdf',
                            ]);
                        } elseif ($fileExtension === 'html') {
                            $message->attach($pdfPath, [
                                'as' => "salary_slip_{$month}.html",
                                'mime' => 'text/html',
                            ]);
                        } else {
                            $message->attach($pdfPath, [
                                'as' => "salary_slip_{$month}.txt",
                                'mime' => 'text/plain',
                            ]);
                        }
                    }
                });
                
                $emailSent = true;
                Log::info("Auto Email sent successfully to {$employeeEmail} for {$employeeName} - {$month}");
                
            } catch (TransportException $transportException) {
                Log::error('Auto Email Transport Exception: ' . $transportException->getMessage());
                $errorMessage = 'SMTP Connection Failed: ' . $transportException->getMessage();
                
            } catch (\Exception $mailException) {
                Log::error('Auto Email sending failed: ' . $mailException->getMessage());
                $errorMessage = 'Email Error: ' . $mailException->getMessage();
            }
            
            // Clean up temporary PDF file
            if ($pdfPath && file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            
            return [
                'success' => $emailSent,
                'message' => $emailSent ? 'Email sent successfully' : $errorMessage
            ];
            
        } catch (\Exception $e) {
            Log::error('Auto Email general error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage()
            ];
        }
    }
    
    public function enablePayroll($id)
    {
        try {
            $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
            
            // Try to find existing payroll record
            $payroll = Payroll::where('workspace_id', $workspaceId)->find($id);
            
            if ($payroll) {
                // If payroll record exists, enable it
                $payroll->update(['is_enabled' => true]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Payroll entry enabled successfully',
                    'data' => $payroll
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payroll record not found. Please create a payroll entry first.'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error enabling payroll entry: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function disablePayroll($id)
    {
        try {
            $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
            
            // First, try to find existing payroll record
            $payroll = Payroll::where('workspace_id', $workspaceId)->find($id);
            
            if ($payroll) {
                // If payroll record exists, disable it
                $payroll->update(['is_enabled' => false]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Payroll entry disabled successfully',
                    'data' => $payroll
                ]);
            } else {
                // If no payroll record exists, check if this is an employee ID
                $employee = User::where('workspace_id', $workspaceId)
                    ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
                    ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                    ->select(
                        'users.id',
                        'users.name',
                        'users.email',
                        'departments.name as department_name'
                    )
                    ->where('users.id', $id)
                    ->first();
                
                if ($employee) {
                    // Create a disabled payroll record for this employee
                    $payroll = Payroll::create([
                        'workspace_id' => $workspaceId,
                        'employee_id' => $employee->id,
                        'name' => $employee->name,
                        'email_address' => $employee->email,
                        'department' => $employee->department_name ?? 'N/A',
                        'month' => 'August 2025',
                        'sal_previous' => 0,
                        'increment' => 0,
                        'salary_current' => 0,
                        'productive_hrs' => 0,
                        'etc_hours' => 0,
                        'atc_hours' => 0,
                        'incentive' => 0,
                        'payable' => 0,
                        'advance' => 0,
                        'total_payable' => 0,
                        'payment_done' => false,
                        'is_enabled' => false
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Employee moved to archive successfully',
                        'data' => $payroll
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Employee not found'
                    ], 404);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error disabling payroll entry: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function archiveAsContractual($id)
    {
        try {
            $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
            
            // First, try to find existing payroll record
            $payroll = Payroll::where('workspace_id', $workspaceId)->find($id);
            
            if ($payroll) {
                // If payroll record exists, mark it as contractual
                $payroll->update([
                    'is_enabled' => false,
                    'is_contractual' => true
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Employee moved to contractual successfully',
                    'data' => $payroll
                ]);
            } else {
                // If no payroll record exists, check if this is an employee ID
                $employee = User::where('workspace_id', $workspaceId)
                    ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
                    ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                    ->select(
                        'users.id',
                        'users.name',
                        'users.email',
                        'departments.name as department_name'
                    )
                    ->where('users.id', $id)
                    ->first();
                
                if ($employee) {
                    // Create a contractual payroll record for this employee
                    $payroll = Payroll::create([
                        'workspace_id' => $workspaceId,
                        'employee_id' => $employee->id,
                        'name' => $employee->name,
                        'email_address' => $employee->email,
                        'department' => $employee->department_name ?? 'N/A',
                        'month' => 'August 2025',
                        'sal_previous' => 0,
                        'increment' => 0,
                        'salary_current' => 0,
                        'productive_hrs' => 0,
                        'etc_hours' => 0,
                        'atc_hours' => 0,
                        'incentive' => 0,
                        'payable' => 0,
                        'advance' => 0,
                        'total_payable' => 0,
                        'payment_done' => false,
                        'is_enabled' => false,
                        'is_contractual' => true
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Employee moved to contractual successfully',
                        'data' => $payroll
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Employee not found'
                    ], 404);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error moving employee to contractual: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function moveToContractual($id)
    {
        try {
            $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
            
            // Find the archived payroll record
            $payroll = Payroll::where('workspace_id', $workspaceId)
                ->where('id', $id)
                ->where('is_enabled', false)
                ->where('is_contractual', false)
                ->first();
            
            if (!$payroll) {
                return response()->json([
                    'success' => false,
                    'message' => 'Archived payroll record not found'
                ], 404);
            }
            
            // Move from archive to contractual
            $payroll->update([
                'is_contractual' => true
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Employee moved from archive to contractual successfully',
                'data' => $payroll
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error moving employee to contractual: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function moveToArchive($id)
    {
        try {
            $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
            
            // Find the contractual payroll record
            $payroll = Payroll::where('workspace_id', $workspaceId)
                ->where('id', $id)
                ->where('is_contractual', true)
                ->first();
            
            if (!$payroll) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contractual payroll record not found'
                ], 404);
            }
            
            // Move from contractual to archive
            $payroll->update([
                'is_contractual' => false,
                'is_enabled' => false
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Employee moved from contractual to archive successfully',
                'data' => $payroll
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error moving employee to archive: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function restore($id)
{
    try {
        $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
        $payroll = Payroll::where('workspace_id', $workspaceId)->findOrFail($id);
        $payroll->update([
            'is_enabled' => true,
            'is_contractual' => false  // Reset contractual status when restoring
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Payroll entry restored successfully',
            'data' => $payroll
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error restoring payroll entry: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * API endpoint to get active payroll data
     */
    public function getActivePayrollData(Request $request)
    {
        try {
            $workspaceId = getActiveWorkSpace();
            $selectedMonth = $request->get('month', 'August 2025');
            
            // Get all employees from the workspace
            $employees = User::where('workspace_id', $workspaceId)
                ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
                ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                ->select(
                    'users.id', 'users.name', 'users.email as email_address',
                    'employees.employee_id', 'departments.name as department'
                )
                ->whereNotNull('employees.user_id')
                ->orderBy('users.name', 'asc')
                ->get();
            
            // Get all payroll records for this workspace for the selected month
            $payrollRecords = Payroll::where('workspace_id', $workspaceId)
                ->where('month', $selectedMonth)
                ->get()
                ->groupBy('employee_id');
            
            // Get previous month name for sal_previous calculation
            $previousMonth = $this->getPreviousMonth($selectedMonth);
            
            // Get previous month payroll records for sal_previous calculation
            $previousMonthRecords = Payroll::where('workspace_id', $workspaceId)
                ->where('month', $previousMonth)
                ->get()
                ->groupBy('employee_id');
            
            // Get all archived employees (from any month) to exclude from main payroll list
            $archivedEmployeeIds = Payroll::where('workspace_id', $workspaceId)
                ->where('is_enabled', false)
                ->pluck('employee_id')
                ->unique()
                ->toArray();
            
            // Filter employees: only show those who are NOT archived and have ENABLED payroll records or no payroll records at all
            $payrolls = $employees->filter(function($employee) use ($payrollRecords, $selectedMonth, $archivedEmployeeIds) {
                // Exclude archived employees from main payroll list
                if (in_array($employee->employee_id, $archivedEmployeeIds)) {
                    return false;
                }
                
                $employeePayrolls = $payrollRecords->get($employee->employee_id, collect());
                $currentMonthPayroll = $employeePayrolls->first();
                return !$currentMonthPayroll || ($currentMonthPayroll && $currentMonthPayroll->is_enabled);
            })->map(function($employee) use ($payrollRecords, $previousMonthRecords, $selectedMonth, $previousMonth) {
                $employeePayrolls = $payrollRecords->get($employee->employee_id, collect());
                $employeePreviousPayrolls = $previousMonthRecords->get($employee->employee_id, collect());
                $currentMonthPayroll = $employeePayrolls->first();
                $previousMonthPayroll = $employeePreviousPayrolls->first();
                
                // Get ETC and ATC data for the employee
                $etcAtcData = $this->getEmployeeETCAndATC($employee->email_address, $selectedMonth);
                
                // Get TeamLogger data for the employee
                $teamLoggerData = $this->getEmployeeTeamLoggerData($employee->email_address, $selectedMonth);
                
                if ($currentMonthPayroll) {
                    // Update sal_previous with previous month's salary_current if available
                    $salPrevious = $previousMonthPayroll ? $previousMonthPayroll->salary_current : $currentMonthPayroll->sal_previous;
                    $currentMonthPayroll->sal_previous = $salPrevious;
                    return $currentMonthPayroll;
                } else {
                    return (object) [
                        'id' => null,
                        'name' => $employee->name,
                        'employee_id' => $employee->employee_id,
                        'department' => $employee->department,
                        'email_address' => $employee->email_address,
                        'month' => $selectedMonth,
                        'sal_previous' => $previousMonthPayroll->salary_current ?? 0,
                        'increment' => 0,
                        'salary_current' => $previousMonthPayroll->salary_current ?? 0,
                        'total_hours' => $teamLoggerData['total_hours'],
                        'idle_hours' => $teamLoggerData['idle_hours'],
                        'productive_hrs' => $teamLoggerData['hours'], // hours = totalHours - idleHours
                        'etc_hours' => $etcAtcData['etc'],
                        'atc_hours' => $etcAtcData['atc'],
                        'approved_hrs' => $teamLoggerData['hours'], // hours = totalHours - idleHours
                        'approval_status' => 'pending',
                        'incentive' => 0,
                        'advance' => 0,
                        'extra' => 0,
                        'payable' => 0,
                        'total_payable' => 0,
                        'payment_done' => false,
                        'is_enabled' => true
                    ];
                }
            });
            
            return response()->json([
                'success' => true,
                'data' => $payrolls->values(),
                'selectedMonth' => $selectedMonth
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching active payroll data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API endpoint to get archive payroll data
     */
    public function getArchivePayrollData(Request $request)
    {
        try {
            $workspaceId = getActiveWorkSpace();
            $selectedMonth = $request->get('month', 'August 2025');
            
            // Get all disabled payroll records (excluding contractual) to show cumulative archive
            // This ensures that once an employee is archived, they stay archived until manually restored
            $disabledPayrolls = Payroll::where('workspace_id', $workspaceId)
                ->where('is_enabled', false)
                ->where('is_contractual', false) // Exclude contractual employees from archive
                ->orderBy('month', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('employee_id')
                ->map(function($employeePayrolls) {
                    // Return the most recent payroll record for each employee
                    return $employeePayrolls->first();
                })
                ->values();
            
            return response()->json([
                'success' => true,
                'data' => $disabledPayrolls,
                'selectedMonth' => $selectedMonth
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching archive payroll data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API endpoint to get contractual payroll data
     */
    public function getContractualPayrollData(Request $request)
    {
        try {
            $workspaceId = getActiveWorkSpace();
            $selectedMonth = $request->get('month', 'August 2025');
            
            // Get all contractual payroll records (regardless of month) to show cumulative contractual list
            // This ensures that once an employee is moved to contractual, they stay there until manually restored
            $contractualPayrolls = Payroll::where('workspace_id', $workspaceId)
                ->where('is_contractual', true)
                ->orderBy('month', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('employee_id')
                ->map(function($employeePayrolls) {
                    // Return the most recent payroll record for each employee
                    return $employeePayrolls->first();
                })
                ->values();
            
            return response()->json([
                'success' => true,
                'data' => $contractualPayrolls,
                'selectedMonth' => $selectedMonth
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching contractual payroll data: ' . $e->getMessage()
            ], 500);
        }
    }
    public function generatePDF($id)
    {
        // Clear any output buffers to ensure clean PDF generation
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        try {
            $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
            
            // Get payroll record with employee details
            $payroll = Payroll::where('workspace_id', $workspaceId)->findOrFail($id);
            
            // Get employee details
            $employee = User::leftJoin('employees', 'users.id', '=', 'employees.user_id')
                ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                ->where('users.id', $payroll->employee_id)
                ->select(
                    'users.name',
                    'users.email as email_address',
                    'departments.name as department'
                )
                ->first();
            
            // Calculate productive hours from total_hours and idle_hours stored in payroll record
            // Productive Hours = TTL HR (total_hours) - IDLE (idle_hours)
            $totalHours = $payroll->total_hours ?? 0;
            $idleHours = $payroll->idle_hours ?? 0;
            $productiveHrs = max(0, $totalHours - $idleHours); // Ensure non-negative
            
            // Set productive_hrs on payroll object BEFORE enrichment so enrichPayrollForSalarySlip uses calculated value
            // This ensures the calculated value is preserved and only uses TeamLogger as fallback if still 0
            $payroll->setAttribute('productive_hrs', $productiveHrs);
            
            // Use the enrichPayrollForSalarySlip method to ensure data is correct
            // This will fetch TeamLogger data if approved_hrs is missing or if productive_hrs is still 0
            $this->enrichPayrollForSalarySlip($payroll);
            
            // Get productive hours (may have been enriched if it was 0)
            $productiveHrs = $payroll->productive_hrs ?? $productiveHrs;
            
            // Get approved hours (enriched if needed)
            $approvedHrs = $payroll->approved_hrs ?? 0;
            
            // Log for debugging (can be removed later)
            \Log::info("PDF Generation - Employee: {$payroll->email_address}, Approved Hrs: {$approvedHrs}, Productive Hrs: {$productiveHrs}");
            
            // Verify and recalculate payable amounts if needed
            $calculatedPayable = 0;
            $calculatedTotalPayable = 0;
            
            if ($payroll->approval_status === 'approved' && $approvedHrs > 0 && $payroll->salary_current > 0) {
                // Formula: Payable = (Current Salary * Approved Hours / 200) - incentive is NOT included
                $calculatedPayable = ($payroll->salary_current * $approvedHrs / 200);
                // Total Payable = Payable + Incentive - Advance + Extra
                $calculatedTotalPayable = $calculatedPayable + ($payroll->incentive ?? 0) - ($payroll->advance ?? 0) + ($payroll->extra ?? 0);
            } else if ($payroll->incentive > 0) {
                // If not approved but has incentive, payable is 0, incentive goes to total payable
                $calculatedPayable = 0;
                $calculatedTotalPayable = ($payroll->incentive ?? 0) - ($payroll->advance ?? 0) + ($payroll->extra ?? 0);
            }
            
            // Use calculated values if they differ significantly from stored values
            if (abs($calculatedPayable - ($payroll->payable ?? 0)) > 1) {
                $payroll->payable = round($calculatedPayable);
            }
            
            if (abs($calculatedTotalPayable - ($payroll->total_payable ?? 0)) > 1) {
                $payroll->total_payable = round($calculatedTotalPayable);
            }
            
            // Prepare data for the blade template
            $data = [
                'payroll' => $payroll,
                'employee' => $employee,
                'productive_hrs' => $productiveHrs,
                'approved_hrs' => $approvedHrs
            ];
            
            // Generate PDF and save temporarily, then return for download
            // This ensures the PDF is valid before sending
            $tempPath = storage_path('app/temp/');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            
            // Generate unique filename
            $timestamp = now()->format('Y_m_d_H_i_s');
            $tempFilename = "salary_slip_{$payroll->id}_{$timestamp}.pdf";
            $tempFilepath = $tempPath . $tempFilename;
            
            try {
                // Generate PDF
                $pdf = Pdf::loadView('payroll.payslip-pdf', $data);
                
                // Set PDF options for better rendering
                $pdf->setPaper('A4', 'portrait');
                $pdf->setOption('isHtml5ParserEnabled', true);
                $pdf->setOption('isRemoteEnabled', true);
                $pdf->setOption('defaultFont', 'Arial');
                $pdf->setOption('enable-local-file-access', true);
                
                // Save PDF to temporary file first
                $pdf->save($tempFilepath);
                
                // Verify file was created and has content
                if (!file_exists($tempFilepath) || filesize($tempFilepath) == 0) {
                    throw new \Exception('PDF file was not created or is empty');
                }
                
                // Generate download filename
                $downloadFilename = "salary_slip_{$payroll->name}_{$payroll->month}.pdf";
                $downloadFilename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $downloadFilename);
                
                // Return the file for download and delete after sending
                return response()->download($tempFilepath, $downloadFilename, [
                    'Content-Type' => 'application/pdf',
                ])->deleteFileAfterSend(true);
                
            } catch (\Exception $pdfException) {
                // Clean up temp file if it exists
                if (file_exists($tempFilepath)) {
                    @unlink($tempFilepath);
                }
                
                Log::error('PDF generation exception: ' . $pdfException->getMessage(), [
                    'exception_type' => get_class($pdfException),
                    'file' => $pdfException->getFile(),
                    'line' => $pdfException->getLine(),
                    'trace' => $pdfException->getTraceAsString()
                ]);
                
                throw $pdfException;
            }
            
        } catch (\Exception $e) {
            // Clear output buffer on error too
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            Log::error('Generate PDF error: ' . $e->getMessage(), [
                'exception_type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }
    public function archive(Request $request)
{
    try {
        $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
        $selectedMonth = $request->get('month', 'August 2025');
        
        // Get all disabled payroll records (regardless of month) to show cumulative archive
        // This ensures that once an employee is archived, they stay archived until manually restored
        $disabledPayrolls = \App\Models\Payroll::where('workspace_id', $workspaceId)
            ->where('is_enabled', false)
            ->orderBy('month', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('employee_id')
            ->map(function($employeePayrolls) {
                // Return the most recent payroll record for each employee
                return $employeePayrolls->first();
            })
            ->values();
            
        return view('payroll.archive', [
            'disabledPayrolls' => $disabledPayrolls,
            'selectedMonth' => $selectedMonth
        ]);
    } catch (\Exception $e) {
        \Log::error('PayrollController archive error: ' . $e->getMessage());
        return view('payroll.archive', [
            'disabledPayrolls' => collect(),
            'selectedMonth' => 'August 2025'
        ]);
    }
}
    /**
     * Enrich payroll data for salary slip view with TeamLogger fallback
     * Recalculates payable/total_payable if approved_hrs was missing
     */
    private function enrichPayrollForSalarySlip($payroll)
    {
        try {
            // Get employee email for TeamLogger lookup
            $employeeEmail = $payroll->email_address;
            if (!$employeeEmail) {
                $employee = User::find($payroll->employee_id);
                $employeeEmail = $employee ? $employee->email : null;
            }
            
            if (!$employeeEmail || !$payroll->month) {
                return; // Can't enrich without email or month
            }
            
            // Check if approved_hrs or productive_hrs is missing (0 or null)
            $approvedHrs = $payroll->approved_hrs ?? 0;
            $productiveHrs = $payroll->productive_hrs ?? 0;
            
            // Fetch TeamLogger data once if needed
            $teamLoggerData = null;
            if ($approvedHrs == 0 || $productiveHrs == 0) {
                $teamLoggerData = $this->getEmployeeTeamLoggerData($employeeEmail, $payroll->month);
                $teamLoggerHours = $teamLoggerData['hours'] ?? 0;
                
                // Use TeamLogger hours as fallback for productive_hrs if missing
                if ($productiveHrs == 0 && $teamLoggerHours > 0) {
                    $payroll->setAttribute('productive_hrs', $teamLoggerHours);
                    $productiveHrs = $teamLoggerHours;
                }
                
                // Use productive hours as approved hours fallback if approved_hrs is missing
                if ($approvedHrs == 0 && $teamLoggerHours > 0) {
                    $payroll->setAttribute('approved_hrs', $teamLoggerHours);
                    $approvedHrs = $teamLoggerHours;
                    \Log::info("Enriched approved_hrs for {$employeeEmail} in {$payroll->month}: {$teamLoggerHours}");
                }
            }
            
            // Recalculate payable and total_payable if approval status is 'approved'
            if ($payroll->approval_status === 'approved' && $payroll->salary_current > 0 && $approvedHrs > 0) {
                // Payable = (Salary × Hours / 200) - incentive is NOT included
                $payable = ($payroll->salary_current * $approvedHrs / 200);
                // Total Payable = Payable + Incentive - Advance + Extra
                $totalPayable = $payable + ($payroll->incentive ?? 0) - ($payroll->advance ?? 0) + ($payroll->extra ?? 0);
                
                // Update the payroll object (not saved to DB, just for display)
                $payroll->payable = round($payable);
                $payroll->total_payable = round($totalPayable);
            }
        } catch (\Exception $e) {
            \Log::error('Error enriching payroll for salary slip: ' . $e->getMessage(), [
                'payroll_id' => $payroll->id ?? null,
                'employee_id' => $payroll->employee_id ?? null
            ]);
        }
    }
    
    public function salarySlip()
    {
        try {
            $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
            $currentUser = Auth::user();
            
            // Check if current user is admin (president or hr)
            $isPayrollAdmin = $currentUser && in_array(strtolower($currentUser->email), ['president@5core.com', 'hr@5core.com']);
            
            if ($isPayrollAdmin) {
                // Admin users can see all salary slips
                $payrolls = Payroll::where('workspace_id', $workspaceId)
                    ->where('payment_done', true)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                // Calculate productive hours from total_hours and idle_hours for each payroll
                // Productive Hours = TTL HR (total_hours) - IDLE (idle_hours)
                foreach ($payrolls as $payroll) {
                    $totalHours = $payroll->total_hours ?? 0;
                    $idleHours = $payroll->idle_hours ?? 0;
                    $payroll->setAttribute('productive_hrs', max(0, $totalHours - $idleHours));
                    $this->enrichPayrollForSalarySlip($payroll);
                }
                
                // Group payrolls by employee for better display
                $groupedPayrolls = $payrolls->groupBy('employee_id');
                
                // Get employee details for all employees who have payrolls
                $employeeIds = $payrolls->pluck('employee_id')->unique();
                $employees = User::leftJoin('employees', 'users.id', '=', 'employees.user_id')
                    ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                    ->whereIn('users.id', $employeeIds)
                    ->select(
                        'users.id',
                        'users.name',
                        'users.email as email_address',
                        'departments.name as department'
                    )
                    ->get()
                    ->keyBy('id');
                
                return view('payroll.salary-slip-admin', [
                    'groupedPayrolls' => $groupedPayrolls,
                    'employees' => $employees,
                    'currentUser' => $currentUser,
                    'isAdmin' => true
                ]);
            } else {
                // Regular users can only see their own salary slips
                $payrolls = Payroll::where('workspace_id', $workspaceId)
                    ->where('employee_id', $currentUser->id)
                    ->where('payment_done', true)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                // Calculate productive hours from total_hours and idle_hours for each payroll
                // Productive Hours = TTL HR (total_hours) - IDLE (idle_hours)
                foreach ($payrolls as $payroll) {
                    $totalHours = $payroll->total_hours ?? 0;
                    $idleHours = $payroll->idle_hours ?? 0;
                    $payroll->setAttribute('productive_hrs', max(0, $totalHours - $idleHours));
                    $this->enrichPayrollForSalarySlip($payroll);
                }
                
                // Get current user's employee details
                $employee = User::leftJoin('employees', 'users.id', '=', 'employees.user_id')
                    ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                    ->where('users.id', $currentUser->id)
                    ->select(
                        'users.id',
                        'users.name',
                        'users.email as email_address',
                        'departments.name as department'
                    )
                    ->first();
                
                return view('payroll.salary-slip', [
                    'payrolls' => $payrolls,
                    'employee' => $employee,
                    'currentUser' => $currentUser,
                    'isAdmin' => false
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('PayrollController salarySlip error: ' . $e->getMessage());
            
            // Fallback to regular user view
            $currentUser = Auth::user();
            return view('payroll.salary-slip', [
                'payrolls' => collect(),
                'employee' => null,
                'currentUser' => $currentUser,
                'isAdmin' => false
            ]);
        }
    }
    
    
    /**
     * Test email configuration
     */
    public function testEmail(Request $request)
    {
        try {
            $testEmail = $request->input('test_email', 'admin@new-tm.scoremanagement.com');
            
            // Configure mail settings
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => env('MAIL_HOST'),
                'mail.mailers.smtp.port' => env('MAIL_PORT', 465),
                'mail.mailers.smtp.encryption' => env('MAIL_ENCRYPTION', 'ssl'),
                'mail.mailers.smtp.username' => env('MAIL_USERNAME'),
                'mail.mailers.smtp.password' => env('MAIL_PASSWORD'),
                'mail.mailers.smtp.timeout' => 60,
                'mail.mailers.smtp.verify_peer' => false,
                'mail.mailers.smtp.verify_peer_name' => false,
                'mail.from.address' => env('MAIL_FROM_ADDRESS'),
                'mail.from.name' => env('MAIL_FROM_NAME', '5 Core Management')
            ]);

            Log::info('Testing email configuration', [
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'username' => config('mail.mailers.smtp.username') ? 'Set' : 'Not Set',
                'from_address' => config('mail.from.address')
            ]);

            Mail::raw('This is a test email to verify mail configuration is working correctly.', function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('Mail Configuration Test - ' . now()->format('Y-m-d H:i:s'));
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $testEmail
            ]);

        } catch (\Exception $e) {
            Log::error('Test email failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Test email failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendSalarySlipEmail(Request $request, $id)
{
    try {
        $request->validate([
            'employee_name' => 'required|string',
            'employee_email' => 'required|email',
            'month' => 'required|string'
        ]);

        $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
        $payroll = Payroll::where('workspace_id', $workspaceId)->findOrFail($id);

        if (!$payroll->payment_done) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot send salary slip. Payment is not marked as done.'
            ], 400);
        }

        $employeeName = $request->employee_name;
        $employeeEmail = $request->employee_email;
        $month = $request->month;

        // Generate PDF file using your existing function
        $pdfPath = $this->generateSalarySlipPDF($payroll);

        // -------------------------------------------------------
        // PHP MAIL() starts here
        // -------------------------------------------------------

        $to = $employeeEmail;
        $subject = "Salary Slip for {$month}";

        $messageBody = "
        Dear {$employeeName},

        Please find attached your salary slip for {$month}.

        If you have any concerns, contact HR/Accounts.

        Regards,
        5CORE INC
        ";

        // Read PDF file
        $file = file_get_contents($pdfPath);
        $content = chunk_split(base64_encode($file));
        $uid = md5(uniqid(time()));

        // Headers
        $fromEmail = "admin@new-tm.5coremanagement.com";
        $fromName = "5CORE INC";

        $header = "From: {$fromName} <{$fromEmail}>\r\n";
        $header .= "Reply-To: {$fromEmail}\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";

        // Message Body
        $message = "--".$uid."\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $messageBody."\r\n\r\n";

        // Attachment
        $filename = "salary_slip_{$month}.pdf";
        $message .= "--".$uid."\r\n";
        $message .= "Content-Type: application/pdf; name=\"".$filename."\"\r\n";
        $message .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "X-Attachment-Id: ".rand(1000,9999)."\r\n\r\n";
        $message .= $content."\r\n\r\n";
        $message .= "--".$uid."--";

        // Send email
        if (mail($to, $subject, $message, $header)) {
            unlink($pdfPath); // delete temp pdf

            return response()->json([
                'success' => true,
                'message' => "Salary slip sent successfully to {$employeeEmail}"
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "PHP mail() failed to send email"
            ], 500);
        }

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

    
    private function generateSalarySlipPDF($payroll)
    {
        try {
            // Get employee data from payroll or fetch from User model
            $employee = (object) [
                'name' => $payroll->name,
                'department' => $payroll->department ?? 'N/A',
                'email_address' => $payroll->email_address ?? 'N/A'
            ];
            
            // Calculate productive hours from total_hours and idle_hours stored in payroll record
            // Productive Hours = TTL HR (total_hours) - IDLE (idle_hours)
            $totalHours = $payroll->total_hours ?? 0;
            $idleHours = $payroll->idle_hours ?? 0;
            $productiveHrs = max(0, $totalHours - $idleHours); // Ensure non-negative
            
            // Set productive_hrs on payroll object BEFORE enrichment so enrichPayrollForSalarySlip uses calculated value
            // This ensures the calculated value is preserved and only uses TeamLogger as fallback if still 0
            $payroll->setAttribute('productive_hrs', $productiveHrs);
            
            // Use the enrichPayrollForSalarySlip method to ensure data is correct
            // This will fetch TeamLogger data if approved_hrs is missing or if productive_hrs is still 0
            $this->enrichPayrollForSalarySlip($payroll);
            
            // Get productive hours (may have been enriched if it was 0)
            $productiveHrs = $payroll->productive_hrs ?? $productiveHrs;
            
            // Get approved hours (enriched if needed)
            $approvedHrs = $payroll->approved_hrs ?? 0;
            
            // Verify and recalculate payable amounts if needed
            // This ensures calculations are always correct in the PDF
            $calculatedPayable = 0;
            $calculatedTotalPayable = 0;
            
            if ($payroll->approval_status === 'approved' && $approvedHrs > 0 && $payroll->salary_current > 0) {
                // Formula: Payable = (Current Salary * Approved Hours / 200) - incentive is NOT included
                $calculatedPayable = ($payroll->salary_current * $approvedHrs / 200);
                // Total Payable = Payable + Incentive - Advance + Extra
                $calculatedTotalPayable = $calculatedPayable + ($payroll->incentive ?? 0) - ($payroll->advance ?? 0) + ($payroll->extra ?? 0);
            } else if ($payroll->incentive > 0) {
                // If not approved but has incentive, payable is 0, incentive goes to total payable
                $calculatedPayable = 0;
                $calculatedTotalPayable = ($payroll->incentive ?? 0) - ($payroll->advance ?? 0) + ($payroll->extra ?? 0);
            }
            
            // Use calculated values if they differ significantly from stored values (more than 1 rupee difference)
            // This helps catch calculation errors
            if (abs($calculatedPayable - ($payroll->payable ?? 0)) > 1) {
                Log::warning("Payroll calculation mismatch for payroll ID {$payroll->id}: Stored payable = {$payroll->payable}, Calculated = {$calculatedPayable}");
                // Use calculated value for PDF
                $payroll->payable = round($calculatedPayable);
            }
            
            if (abs($calculatedTotalPayable - ($payroll->total_payable ?? 0)) > 1) {
                Log::warning("Payroll total calculation mismatch for payroll ID {$payroll->id}: Stored total = {$payroll->total_payable}, Calculated = {$calculatedTotalPayable}");
                // Use calculated value for PDF
                $payroll->total_payable = round($calculatedTotalPayable);
            }
            
            // Prepare data for the blade template
            // Pass both the enriched payroll object and explicit variables for redundancy
            $data = [
                'payroll' => $payroll,
                'employee' => $employee,
                'productive_hrs' => $productiveHrs,
                'approved_hrs' => $approvedHrs
            ];
            
            // Generate PDF using the existing payslip-pdf blade template
            $pdf = Pdf::loadView('payroll.payslip-pdf', $data);
            
            // Set PDF options for better rendering
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);
            $pdf->setOption('defaultFont', 'Arial');
            
            // Create temporary directory if it doesn't exist
            $tempPath = storage_path('app/temp/');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            
            // Generate a unique filename
            $timestamp = now()->format('Y_m_d_H_i_s');
            $filename = "salary_slip_{$payroll->name}_{$payroll->month}_{$timestamp}.pdf";
            // Clean filename - remove special characters
            $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
            $filepath = $tempPath . $filename;
            
            // Save the PDF file
            $pdf->save($filepath);
            
            Log::info("PDF salary slip generated: {$filepath} for employee: {$payroll->name}");
            
            return $filepath;
            
        } catch (\Exception $e) {
            Log::error('PDF salary slip generation error: ' . $e->getMessage(), [
                'exception_type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback: create a simple PDF using DomPDF with inline HTML
            try {
                $fallbackHtml = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Salary Slip</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .company-name { font-size: 24px; font-weight: bold; color: #333; }
                        .document-title { font-size: 20px; margin: 10px 0; }
                        .employee-info { margin: 20px 0; }
                        .info-row { margin: 8px 0; }
                        .label { font-weight: bold; display: inline-block; width: 150px; }
                        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                        th { background-color: #f5f5f5; font-weight: bold; }
                        .total-row { background-color: #e8f4fd; font-weight: bold; }
                        .footer { margin-top: 40px; text-align: center; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <div class="company-name">5CORE INC</div>
                        <div class="document-title">SALARY SLIP</div>
                        <div>1221 W Sandusky Ave Suite C, Bellefontaine OH 43311 | +19513836157</div>
                    </div>
                    
                    <div class="employee-info">
                        <div class="info-row"><span class="label">Employee Name:</span> ' . htmlspecialchars($payroll->name) . '</div>
                        <div class="info-row"><span class="label">Department:</span> ' . htmlspecialchars($payroll->department ?? 'N/A') . '</div>
                        <div class="info-row"><span class="label">Email:</span> ' . htmlspecialchars($payroll->email_address ?? 'N/A') . '</div>
                        <div class="info-row"><span class="label">Month:</span> ' . htmlspecialchars($payroll->month) . '</div>
                    </div>
                    
                    <table>
                        <tr><th>Description</th><th>Amount</th></tr>
                        <tr><td>Salary Previous</td><td>' . number_format($payroll->sal_previous ?? 0, 0) . '</td></tr>
                        <tr><td>Increment</td><td>' . number_format($payroll->increment ?? 0, 0) . '</td></tr>
                        <tr><td>Current Salary</td><td>' . number_format($payroll->salary_current ?? 0, 0) . '</td></tr>
                        <tr><td>Productive Hrs</td><td>' . ($payroll->productive_hrs ?? '0') . ' hr</td></tr>
                        <tr><td>Incentive</td><td>' . number_format($payroll->incentive ?? 0, 0) . '</td></tr>
                        <tr><td>Payable Amount</td><td>' . number_format($payroll->payable ?? 0, 0) . '</td></tr>
                        <tr><td>Deduction</td><td>' . number_format($payroll->advance ?? 0, 0) . '</td></tr>
                        <tr><td>Extra</td><td>' . number_format($payroll->extra ?? 0, 0) . '</td></tr>
                        <tr class="total-row"><td><strong>Total Payable Rs</strong></td><td><strong>' . number_format($payroll->total_payable ?? 0, 0) . '</strong></td></tr>
                    </table>
                    
                    <div style="margin-top: 40px;">
                        <div style="display: inline-block; width: 45%;">
                            <div style="margin-top: 60px; border-top: 1px solid #000; text-align: center; padding-top: 5px;">Employee Signature</div>
                        </div>
                        <div style="display: inline-block; width: 45%; float: right;">
                            <div style="margin-top: 60px; border-top: 1px solid #000; text-align: center; padding-top: 5px;">HR Department</div>
                        </div>
                    </div>
                    
                    <div class="footer">
                        <p>If you have any queries, Please contact our HR team by dropping an email at hr@5core.com</p>
                        <p>This is a system-generated PDF and does not require a response.</p>
                        <p><strong>Generated:</strong> ' . now()->format('d M Y, H:i:s') . '</p>
                    </div>
                </body>
                </html>';
                
                $fallbackPdf = Pdf::loadHTML($fallbackHtml);
                $fallbackPdf->setPaper('A4', 'portrait');
                
                $tempPath = storage_path('app/temp/');
                if (!file_exists($tempPath)) {
                    mkdir($tempPath, 0755, true);
                }
                
                $timestamp = now()->format('Y_m_d_H_i_s');
                $filename = "salary_slip_fallback_{$payroll->name}_{$payroll->month}_{$timestamp}.pdf";
                $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
                $filepath = $tempPath . $filename;
                
                $fallbackPdf->save($filepath);
                
                Log::info("Fallback PDF salary slip generated: {$filepath} for employee: {$payroll->name}");
                
                return $filepath;
                
            } catch (\Exception $fallbackException) {
                Log::error('Fallback PDF generation also failed: ' . $fallbackException->getMessage());
                
                // Last resort: create a text file
                $tempPath = storage_path('app/temp/');
                if (!file_exists($tempPath)) {
                    mkdir($tempPath, 0755, true);
                }
                
                $filename = 'salary_slip_' . $payroll->employee_id . '_' . time() . '.txt';
                $filepath = $tempPath . $filename;
                
                $textContent = str_repeat("=", 60) . "\n";
                $textContent .= "                    SALARY SLIP\n";
                $textContent .= "                 5CORE INC\n";
                $textContent .= str_repeat("=", 60) . "\n\n";
                
                $textContent .= "EMPLOYEE INFORMATION:\n";
                $textContent .= str_repeat("-", 30) . "\n";
                $textContent .= sprintf("%-20s: %s\n", "Employee Name", $payroll->name);
                $textContent .= sprintf("%-20s: %s\n", "Employee ID", $payroll->employee_id);
                $textContent .= sprintf("%-20s: %s\n", "Department", $payroll->department);
                $textContent .= sprintf("%-20s: %s\n", "Email", $payroll->email_address);
                $textContent .= sprintf("%-20s: %s\n", "Pay Period", $payroll->month);
                $textContent .= sprintf("%-20s: %s\n", "Payment Date", $payroll->updated_at->format('d M Y'));
                
                $textContent .= "\nSALARY BREAKDOWN:\n";
                $textContent .= str_repeat("-", 30) . "\n";
                $textContent .= sprintf("%-25s: ₹%12s\n", "Previous Salary", number_format($payroll->sal_previous, 2));
                $textContent .= sprintf("%-25s: ₹%12s\n", "Increment", number_format($payroll->increment, 2));
                $textContent .= sprintf("%-25s: ₹%12s\n", "Current Salary", number_format($payroll->salary_current, 2));
                $textContent .= sprintf("%-25s: %14s hrs\n", "Productive Hours", $payroll->productive_hrs);
                $textContent .= sprintf("%-25s: ₹%12s\n", "Incentive", number_format($payroll->incentive, 2));
                $textContent .= sprintf("%-25s: ₹%12s\n", "Gross Payable", number_format($payroll->payable, 2));
                $textContent .= sprintf("%-25s: ₹%12s\n", "Advance Deduction", number_format($payroll->advance, 2));
                $textContent .= str_repeat("-", 40) . "\n";
                $textContent .= sprintf("%-25s: ₹%12s\n", "NET PAYABLE", number_format($payroll->total_payable, 2));
                $textContent .= str_repeat("=", 40) . "\n\n";
                
                $textContent .= "Generated: " . now()->format('d M Y H:i:s') . "\n";
                $textContent .= "Reference: PAY-" . $payroll->id . "-" . date('Y') . "\n";
                $textContent .= "Contact: hr@5core.com\n";
                
                file_put_contents($filepath, $textContent);
                
                return $filepath;
            }
        }
    }

    /**
     * Get ETC and ATC data for an employee based on month
     * Uses EXACT same logic as Task Done List ProjectController::doneTaskCountData
     */
    private function getEmployeeETCAndATC($employeeEmail, $month)
    {
        try {
            // Parse month to get year and month number
            $monthParts = explode(' ', $month);
            if (count($monthParts) != 2) {
                return ['etc' => 0, 'atc' => 0];
            }
            
            $monthName = $monthParts[0];
            $year = $monthParts[1];
            
            // Convert month name to number
            $monthNumber = date('m', strtotime($monthName . ' 1'));
            
            // Get active workspace ID (using the same method as ProjectController)
            $workspaceId = getActiveWorkSpace();
            
            \Log::info("ETC/ATC Debug: Querying for email={$employeeEmail}, workspace={$workspaceId}, month={$monthNumber}, year={$year}");
            
            // Use the EXACT same base query as ProjectController::doneTaskCountData
            $taskBaseQuery = Task::select('tasks.*', 'stages.name as stage_name', 'assignor_users.name as assigner_name')
                ->join('stages', 'stages.name', '=', 'tasks.status')
                ->where('tasks.is_missed', 0)
                ->whereNotNull('tasks.deleted_at')
                ->where('tasks.status', 'Done')
                ->leftJoin('users as assignor_users', 'assignor_users.email', '=', 'tasks.assignor')
                ->where('tasks.workspace', $workspaceId)
                ->groupBy('tasks.id');
            
            // Apply month filter using completion_date (same as Task Done List)
            $taskBaseQuery->whereMonth('tasks.completion_date', $monthNumber)
                          ->whereYear('tasks.completion_date', $year);
            
            // Filter by assignee email - use EXACT same logic as ProjectController
            // Only filter by assign_to (assignee), not assignor
            $taskBaseQuery->where('tasks.assign_to', 'like', "%{$employeeEmail}%");
            
            // Debug: Log the exact query count and first few task IDs
            $taskCount = (clone $taskBaseQuery)->count();
            $taskIds = (clone $taskBaseQuery)->pluck('id')->take(5)->toArray();
            \Log::info("ETC/ATC Query Debug: email={$employeeEmail}, task_count={$taskCount}, sample_ids=" . implode(',', $taskIds));
            
            // Use the EXACT same calculation logic as ProjectController::doneTaskCountData
            $totalETAmin = collect((clone $taskBaseQuery)->where('eta_time', '>', 0)->pluck('eta_time')->toArray())->sum();
            $totalATCMin = collect((clone $taskBaseQuery)->where('etc_done', '>', 0)->pluck('etc_done')->toArray())->sum();
            
            // Convert minutes to hours (round to whole numbers)
            $etcHours = round($totalETAmin / 60);
            $atcHours = round($totalATCMin / 60);
            
            \Log::info("ETC/ATC Final: email={$employeeEmail}, ETC={$etcHours}hrs ({$totalETAmin}min), ATC={$atcHours}hrs ({$totalATCMin}min)");
            
            return [
                'etc' => $etcHours,
                'atc' => $atcHours
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting ETC/ATC data: ' . $e->getMessage());
            return ['etc' => 0, 'atc' => 0];
        }
    }
    
    /**
     * Get overdue count for an employee
     * Counts all tasks that are currently overdue
     * Uses EXACT same logic as Task Board ProjectController::taskTracklist overdue calculation
     */
    private function getEmployeeOverdueCount($employeeEmail, $month)
    {
        try {
            // Get active workspace ID
            $workspaceId = getActiveWorkSpace();
            
            // Count overdue tasks - EXACT same logic as Task Board "Overdue" filter
            // From ProjectController line 3157-3160:
            // - is_missed = 0 (not the overdue flag, confusingly named)
            // - status != '' (has a status)
            // - due_date < now() (past due date)
            // - deleted_at IS NULL (not soft deleted)
            $overdueCount = Task::where('workspace', $workspaceId)
                ->where('is_missed', 0)
                ->where('status', '!=', '')
                ->where('due_date', '<', now())
                ->whereNull('deleted_at')
                ->where('assign_to', 'like', "%{$employeeEmail}%")
                ->count();
            
            \Log::info("Overdue Count Debug: email={$employeeEmail}, count={$overdueCount}");
            
            return $overdueCount;
        } catch (\Exception $e) {
            \Log::error('Error getting overdue count: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * OPTIMIZED: Fetch TeamLogger data for ALL employees in one API call
     * Returns array indexed by email address (lowercase)
     */
    private function getAllTeamLoggerDataBatch($month)
    {
        try {
            // Parse month to get year and month number
            $monthParts = explode(' ', $month);
            if (count($monthParts) != 2) {
                return [];
            }

            $monthName = $monthParts[0];
            $year = (int) $monthParts[1];
            $monthNumber = (int) date('m', strtotime($monthName . ' 1'));

            // Create cache key for this month's TeamLogger data
            $cacheKey = "teamlogger_batch_{$year}_{$monthNumber}";

            // Use static cache to avoid multiple API calls in same request
            static $batchCache = [];

            if (isset($batchCache[$cacheKey])) {
                \Log::info("TeamLogger Batch: Using cached data for {$month}");
                return $batchCache[$cacheKey];
            }

            // Build date range
            $startDate = \Carbon\Carbon::create($year, $monthNumber, 1)->format('Y-m-d');
            $endDate = \Carbon\Carbon::create($year, $monthNumber)->endOfMonth()->format('Y-m-d');

            $startTime = null;
            $endTime = null;

            if (!empty($startDate)) {
                $startOfDayLocal = \Carbon\Carbon::parse($startDate)->setTime(12, 0, 0);
                $startTime = $startOfDayLocal->copy()->utc()->getTimestamp() * 1000;
            }
            if (!empty($endDate)) {
                $endOfRangeLocal = \Carbon\Carbon::parse($endDate)->addDay()->setTime(11, 59, 59);
                $endTime = $endOfRangeLocal->copy()->utc()->getTimestamp() * 1000;
            }

            // Default to last 30 days if no specific filter
            if (!$startTime || !$endTime) {
                $startTime = now()->subDays(30)->setTime(12, 0, 0)->copy()->utc()->getTimestamp() * 1000;
                $endTime = now()->addDay()->setTime(11, 59, 59)->copy()->utc()->getTimestamp() * 1000;
            }

            // Fetch from API - ONCE for all employees
            $curl = curl_init();
            $apiUrl = "https://api2.teamlogger.com/api/employee_summary_report?startTime={$startTime}&endTime={$endTime}";

            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vaGlwZXJyLmNvbSIsInN1YiI6IjYyNDJhZjhhNmJlMjQ2YzQ5MTcwMmFiYjgyYmY5ZDYwIiwiYXVkIjoic2VydmVyIn0.mRzusxn0Ws9yD7Qmxu9QcFCNiLOnoEXSjy90edAFK4U',
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            \Log::info("TeamLogger Batch API: HTTP={$httpCode}, respLen=" . strlen((string)$response));

            $employeeDataMap = [];

            if ($curlError || $httpCode !== 200 || !$response) {
                \Log::warning("TeamLogger Batch API failed: HTTP={$httpCode}, curlError={$curlError}");
            } else {
                $data = json_decode($response, true);
                if (!is_array($data)) {
                    \Log::warning("TeamLogger Batch API decode failed");
                } else {
                    \Log::info("TeamLogger Batch API: Processing " . count($data) . " records");
                    
                    // Process all employee records
                    foreach ($data as $rec) {
                        $email = $rec['email'] ?? $rec['userEmail'] ?? $rec['user_email'] ?? null;
                        if (!$email || !is_string($email)) continue;
                        
                        $emailKey = strtolower(trim($email));
                        
                        // Handle special case
                        if ($emailKey === 'customercare@5core.com') {
                            $emailKey = 'debhritiksha@gmail.com';
                        }
                        
                        $totalHours = 0;
                        $rawTotalHours = 0;
                        $idleHours = 0;
                        
                        // Extract hours data
                        if (!empty($rec['totalHours'])) {
                            $rawTotalHours = floatval($rec['totalHours']);
                            $idleHours = isset($rec['idleHours']) ? floatval($rec['idleHours']) : 0;
                            $totalHours = $rawTotalHours - $idleHours;
                        } elseif (!empty($rec['onComputerHours'])) {
                            $totalHours = floatval($rec['onComputerHours']);
                        } elseif (!empty($rec['workHours'])) {
                            $totalHours = floatval($rec['workHours']);
                        } elseif (!empty($rec['hours'])) {
                            $totalHours = floatval($rec['hours']);
                        }
                        
                        // Store in map
                        $employeeDataMap[$emailKey] = [
                            'hours' => (int) round($totalHours),
                            'total_hours' => round($rawTotalHours, 2),
                            'idle_hours' => round($idleHours, 2)
                        ];
                    }
                }
            }

            // Cache the result
            $batchCache[$cacheKey] = $employeeDataMap;
            \Log::info("TeamLogger Batch: Cached " . count($employeeDataMap) . " employee records");

            return $employeeDataMap;

        } catch (\Exception $e) {
            \Log::error('Error in TeamLogger batch fetch: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get TeamLogger data for an employee based on month
     * Uses the actual TeamLogger API with caching to prevent timeouts
     */
    private function getEmployeeTeamLoggerData($employeeEmail, $month)
    {
        try {
    if($employeeEmail == "customercare@5core.com" )
    {
      $employeeEmail =  "debhritiksha@gmail.com"; 
    }
    // Parse month to get year and month number
    $monthParts = explode(' ', $month);
    if (count($monthParts) != 2) {
        return ['hours' => 0];
    }

    $monthName = $monthParts[0];
    $year = (int) $monthParts[1];

    // Convert month name to number
    $monthNumber = (int) date('m', strtotime($monthName . ' 1'));

    // Create cache key for this month's TeamLogger data
    $cacheKey = "teamlogger_data_{$year}_{$monthNumber}";

    // Check if we already have cached TeamLogger data for this month (process-local)
    static $teamloggerCache = [];

    // -------------------- Build date range --------------------
    // test code dates (you already had these)
    // $startDate = "2025-10-01";
    // $endDate = "2025-10-31";
    
$startDate = \Carbon\Carbon::create($year, $monthNumber, 1)->format('Y-m-d');
$endDate = \Carbon\Carbon::create($year, $monthNumber)->endOfMonth()->format('Y-m-d');
                // $startTime = $startOfMonth->timestamp * 1000;
                // $endTime = $endOfMonth->timestamp * 1000;

    $startTime = null;
    $endTime = null;

    if (!empty($startDate)) {
        // Custom start: 12:00 PM local on selected date, convert to UTC ms
        $startOfDayLocal = \Carbon\Carbon::parse($startDate)->setTime(12, 0, 0);
        $startTime = $startOfDayLocal->copy()->utc()->getTimestamp() * 1000;
    }
    if (!empty($endDate)) {
        // Custom end: 11:59:59 AM on the day AFTER selected date (keeps your 12:00 -> 11:59 pattern)
        $endOfRangeLocal = \Carbon\Carbon::parse($endDate)->addDay()->setTime(11, 59, 59);
        $endTime = $endOfRangeLocal->copy()->utc()->getTimestamp() * 1000;
    }

    // Default to last 30 days if no specific filter (12:00 PM to 11:59 AM pattern)
    if (!$startTime || !$endTime) {
        $startTime = now()->subDays(30)->setTime(12, 0, 0)->copy()->utc()->getTimestamp() * 1000;
        $endTime = now()->addDay()->setTime(11, 59, 59)->copy()->utc()->getTimestamp() * 1000;
    }

    // -------------------- Fetch from API (or use process cache) --------------------
    if (!isset($teamloggerCache[$cacheKey])) {
        $curl = curl_init();

        $apiUrl = "https://api2.teamlogger.com/api/employee_summary_report?startTime={$startTime}&endTime={$endTime}";

        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vaGlwZXJyLmNvbSIsInN1YiI6IjYyNDJhZjhhNmJlMjQ2YzQ5MTcwMmFiYjgyYmY5ZDYwIiwiYXVkIjoic2VydmVyIn0.mRzusxn0Ws9yD7Qmxu9QcFCNiLOnoEXSjy90edAFK4U',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        \Log::info("TeamLogger API fetch: HTTP={$httpCode}, curlError={$curlError}, respLen=" . strlen((string)$response));

        if ($curlError || $httpCode !== 200 || !$response) {
            \Log::warning("TeamLogger API fetch failed: HTTP={$httpCode}, curlError={$curlError}");
            $teamloggerCache[$cacheKey] = [];
        } else {
            $data = json_decode($response, true);
            if (!is_array($data)) {
                \Log::warning("TeamLogger API decode failed or returned non-array. Raw start: " . substr($response, 0, 2000));
                $teamloggerCache[$cacheKey] = [];
            } else {
                // Store the decoded array in process-local cache
                $teamloggerCache[$cacheKey] = $data;
                \Log::info("TeamLogger API: Cached " . count($data) . " records for key={$cacheKey}");
            }
        }
    }

    // -------------------- Prepare fast lookup map (email => record) --------------------
    $cachedData = $teamloggerCache[$cacheKey] ?? [];
    $employeeMap = [];

    if (is_array($cachedData) && count($cachedData) > 0) {
        foreach ($cachedData as $rec) {
            // robust email extraction
            $email = $rec['email'] ?? $rec['userEmail'] ?? $rec['user_email'] ?? null;
            if (!$email || !is_string($email)) continue;
            $emailKey = strtolower(trim($email));

            // pick the most recent record by lastActivityTime or lastSyncedTime
            $curTs = $rec['lastActivityTime'] ?? $rec['lastSyncedTime'] ?? 0;
            $exist = $employeeMap[$emailKey] ?? null;
            $existTs = $exist['lastActivityTime'] ?? $exist['lastSyncedTime'] ?? 0;

            if (!$exist || ($curTs > $existTs)) {
                $employeeMap[$emailKey] = $rec;
            }
        }
    }

    // -------------------- Lookup requested employee record quickly --------------------
    $lookupEmail = is_string($employeeEmail) ? strtolower(trim($employeeEmail)) : null;
    $matched = $lookupEmail ? ($employeeMap[$lookupEmail] ?? null) : null;

    $totalHours = 0;
    $rawTotalHours = 0;
    $idleHours = 0;

    if ($matched) {
        // Prefer totalHours (minus idleHours), then onComputerHours, then workHours/hours
        if (!empty($matched['totalHours'])) {
            $rawTotalHours = floatval($matched['totalHours']);
            $idleHours = isset($matched['idleHours']) ? floatval($matched['idleHours']) : 0;
            $totalHours = $rawTotalHours - $idleHours;
        } elseif (!empty($matched['onComputerHours'])) {
            $totalHours = floatval($matched['onComputerHours']);
        } elseif (!empty($matched['workHours'])) {
            $totalHours = floatval($matched['workHours']);
        } elseif (!empty($matched['hours'])) {
            $totalHours = floatval($matched['hours']);
        } else {
            $totalHours = 0;
        }

        \Log::info("TeamLogger matched: email={$employeeEmail}, totalHours={$totalHours}, raw={$rawTotalHours}, idle={$idleHours}");
    } else {
        \Log::info("TeamLogger: no matched record for {$employeeEmail} (records=" . count($cachedData) . ")");
    }

    // Special-case user
    // if ($employeeEmail === 'software2@5core.com') {
    //     $totalHours += 7;    // TL Hr adjust
    //     $rawTotalHours += 8; // TTL Hr adjust
    //     \Log::info("TeamLogger Special Adjustment applied for {$employeeEmail}");
    // }

    // If no data found and not the special user, fall back
    if ($totalHours === 0) {
        return $this->getTeamLoggerFallback($employeeEmail, $month);
    }
    
    // if(Auth::user()->email == "software13@5core.com")
    // {
    //     \App\Models\Payroleteamlogger::updateOrCreate(
    //     [
    //         'email_address' => $employeeEmail,
    //         'month' => $month,
    //     ],
    //     [
    //         'productive_hrs' => round($totalHours, 2),
    //         'approved_hrs' => round($totalHours, 2), // modify if you have approved hours separately
    //     ]
    // );
    // }

    return [
        'hours' => (int) round($totalHours),
        'total_hours' => round($rawTotalHours, 2),
        'idle_hours' => round($idleHours, 2)
    ];

} catch (\Exception $e) {
    \Log::error('Error getting TeamLogger API data: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    return $this->getTeamLoggerFallback($employeeEmail, $month);
}

    }
    
    /**
     * Fallback method to calculate TeamLogger hours from database when API fails
     */
    private function getTeamLoggerFallback($employeeEmail, $month)
    {
        try {
            // Calculate fallback based on ETC/ATC activity (same as before but simplified)
            $etcAtcData = $this->getEmployeeETCAndATC($employeeEmail, $month);
            
            // If employee has no ETC/ATC data, return 0 (don't use any minimum)
            if ($etcAtcData['etc'] == 0 && $etcAtcData['atc'] == 0) {
                \Log::info("TeamLogger Fallback: email={$employeeEmail}, no ETC/ATC data found, returning 0");
                
                // Special case for software2@5core.com - return 8 hours even if no ETC/ATC data
                if ($employeeEmail === 'software2@5core.com') {
                    \Log::info("TeamLogger Fallback Special Case: software2@5core.com gets 8 hours minimum");
                    return [
                        'hours' => 8,
                        'total_hours' => 7,
                        'idle_hours' => 0
                    ];
                }
                
                return [
                    'hours' => 0,
                    'total_hours' => 0,
                    'idle_hours' => 0
                ];
            }
            
            $estimatedHours = ($etcAtcData['etc'] + $etcAtcData['atc']) * 1.2;
            
            // Special logic for software2@5core.com - add 8 hours
            if ($employeeEmail === 'software2@5core.com') {
                $estimatedHours += 8;
                \Log::info("TeamLogger Fallback Special Adjustment for software2@5core.com: Added 8 hours - Total: {$estimatedHours}hrs");
            }
            
            \Log::info("TeamLogger Fallback: email={$employeeEmail}, estimated={$estimatedHours}hrs based on ETC/ATC");
            
            return [
                'hours' => min((int) round($estimatedHours), 200),
                'total_hours' => 0,
                'idle_hours' => 0
            ];
        } catch (\Exception $e) {
            \Log::error('Error in TeamLogger fallback: ' . $e->getMessage());
            
            // Special case for software2@5core.com - even if there's an error, return 8 hours
            if ($employeeEmail === 'software2@5core.com') {
                \Log::info("TeamLogger Fallback Error Case: software2@5core.com gets 8 hours minimum");
                return [
                    'hours' => 8,
                    'total_hours' => 8,
                    'idle_hours' => 0
                ];
            }
            
            return ['hours' => 0];
        }
    }
    
    /**
     * Debug method to test ETC and ATC data retrieval
     */
    public function debugETCATC()
    {
        try {
            // Test with Rupak Manna's email and August 2025
            $employeeEmail = 'software2@5core.com'; // Rupak's email from the screenshot
            $month = 'August 2025';
            
            // Parse month for detailed debugging
            $monthParts = explode(' ', $month);
            $monthName = $monthParts[0];
            $year = $monthParts[1];
            $monthNumber = date('m', strtotime($monthName . ' 1'));
            
            // Get workspace ID
            $workspaceId = getActiveWorkSpace();
            
            // Get raw task data for debugging
            $tasks = Task::select('tasks.*', 'stages.name as stage_name')
                ->join('stages', 'stages.name', '=', 'tasks.status')
                ->where('tasks.workspace', $workspaceId)
                ->where('tasks.status', 'Done')
                ->whereNotNull('tasks.deleted_at')
                ->where('tasks.is_missed', 0)
                ->whereYear('tasks.completion_date', $year)
                ->whereMonth('tasks.completion_date', $monthNumber)
                ->where(function ($query) use ($employeeEmail) {
                    $query->whereRaw("FIND_IN_SET(?, tasks.assign_to)", [$employeeEmail])
                          ->orWhere('tasks.assignor', $employeeEmail);
                })
                ->get();
            
            $etcAtcData = $this->getEmployeeETCAndATC($employeeEmail, $month);
            
            return response()->json([
                'employee_email' => $employeeEmail,
                'month' => $month,
                'month_number' => $monthNumber,
                'year' => $year,
                'workspace_id' => $workspaceId,
                'total_tasks_found' => count($tasks),
                'tasks_with_etc' => $tasks->where('eta_time', '>', 0)->count(),
                'tasks_with_atc' => $tasks->where('etc_done', '>', 0)->count(),
                'etc_hours' => $etcAtcData['etc'],
                'atc_hours' => $etcAtcData['atc'],
                'sample_tasks' => $tasks->take(5)->map(function($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'eta_time' => $task->eta_time,
                        'etc_done' => $task->etc_done,
                        'completion_date' => $task->completion_date,
                        'assignor' => $task->assignor,
                        'assign_to' => $task->assign_to
                    ];
                }),
                'debug_info' => 'ETC/ATC data retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function updateApprovedHours(Request $request)
    {
        try {
            $payrollId = $request->input('payroll_id');
            $approvedHours = $request->input('approved_hrs');

            // Validate input
            if (!$payrollId || !is_numeric($approvedHours)) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            // Find and update the payroll record
            $payroll = \App\Models\Payroll::find($payrollId);
            if (!$payroll) {
                return response()->json(['error' => 'Payroll record not found'], 404);
            }

            $payroll->approved_hrs = intval($approvedHours);
            
            // Recalculate payable based on approval status and approved hours
            $payable = 0;
            $totalPayable = 0;
            
            if ($payroll->approval_status === 'approved' && $payroll->salary_current) {
                // Get employee's email to fetch TeamLogger data for fallback
                $employee = User::find($payroll->employee_id);
                if ($employee) {
                    // Get TeamLogger data for fallback
                    $teamLoggerData = $this->getEmployeeTeamLoggerData($employee->email, $payroll->month);
                    
                    // Use approved_hrs if set, otherwise use TeamLogger hours
                    $effectiveHours = $payroll->approved_hrs ?? $teamLoggerData['hours'];
                    
                    if ($effectiveHours > 0) {
                        // Payable = (Salary × Hours / 200) - incentive is NOT included
                        $payable = ($payroll->salary_current * $effectiveHours / 200);
                        // Total Payable = Payable + Incentive - Advance + Extra
                        $totalPayable = $payable + ($payroll->incentive ?? 0) - ($payroll->advance ?? 0) + ($payroll->extra ?? 0);
                    }
                }
            }
            
            $payroll->payable = round($payable);
            $payroll->total_payable = round($totalPayable);
            $payroll->save();

            return response()->json([
                'success' => true,
                'message' => 'Approved hours updated successfully',
                'approved_hours' => $payroll->approved_hrs,
                'payable' => $payroll->payable,
                'total_payable' => $payroll->total_payable
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update approved hours: ' . $e->getMessage()], 500);
        }
    }

    public function updateApprovalStatus(Request $request)
    {
        try {
            $payrollId = $request->input('payroll_id');
            $approvalStatus = $request->input('approval_status');

            // Validate input
            if (!$payrollId || !in_array($approvalStatus, ['pending', 'approved'])) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            // Find and update the payroll record
            $payroll = \App\Models\Payroll::find($payrollId);
            if (!$payroll) {
                return response()->json(['error' => 'Payroll record not found'], 404);
            }

            $payroll->approval_status = $approvalStatus;
            
            // Recalculate payable based on approval status and approved hours
            $payable = 0;
            $totalPayable = 0;
            
            if ($payroll->approval_status === 'approved' && $payroll->salary_current) {
                // Get employee's email to fetch TeamLogger data
                $employee = User::find($payroll->employee_id);
                if ($employee) {
                    // Get TeamLogger data for fallback
                    $teamLoggerData = $this->getEmployeeTeamLoggerData($employee->email, $payroll->month);
                    
                    // Use approved_hrs if set, otherwise use TeamLogger hours
                    $effectiveHours = $payroll->approved_hrs ?? $teamLoggerData['hours'];
                    
                    if ($effectiveHours > 0) {
                        // Payable = (Salary × Hours / 200) - incentive is NOT included
                        $payable = ($payroll->salary_current * $effectiveHours / 200);
                        // Total Payable = Payable + Incentive - Advance + Extra
                        $totalPayable = $payable + ($payroll->incentive ?? 0) - ($payroll->advance ?? 0) + ($payroll->extra ?? 0);
                    }
                }
            }
            
            $payroll->payable = round($payable);
            $payroll->total_payable = round($totalPayable);
            $payroll->save();

            return response()->json([
                'success' => true,
                'message' => 'Approval status updated successfully',
                'approval_status' => $payroll->approval_status,
                'payable' => $payroll->payable,
                'total_payable' => $payroll->total_payable
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update approval status: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Fix missing sal_previous values by fetching from previous month's salary_current
     */
    public function fixMissingSalPrevious(Request $request)
    {
        try {
            $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
            $month = $request->get('month');
            
            if (!$month) {
                return response()->json(['error' => 'Month parameter is required'], 400);
            }
            
            // Get all payroll records for the specified month where sal_previous is null or 0
            $payrolls = Payroll::where('workspace_id', $workspaceId)
                ->where('month', $month)
                ->where(function($query) {
                    $query->whereNull('sal_previous')
                          ->orWhere('sal_previous', 0);
                })
                ->get();
            
            $fixedCount = 0;
            $previousMonth = $this->getPreviousMonth($month);
            
            foreach ($payrolls as $payroll) {
                // Find previous month's payroll for this employee
                $previousMonthPayroll = Payroll::where('employee_id', $payroll->employee_id)
                    ->where('workspace_id', $workspaceId)
                    ->where('month', $previousMonth)
                    ->first();
                
                if ($previousMonthPayroll && $previousMonthPayroll->salary_current > 0) {
                    // Update sal_previous with previous month's salary_current
                    $payroll->sal_previous = $previousMonthPayroll->salary_current;
                    
                    // Recalculate salary_current based on sal_previous + increment
                    $payroll->salary_current = $payroll->sal_previous + ($payroll->increment ?? 0);
                    
                    // Recalculate payable if approved
                    if ($payroll->approval_status === 'approved' && $payroll->approved_hrs > 0) {
                        // Payable = (Salary × Hours / 200) - incentive is NOT included
                        $payable = ($payroll->salary_current * $payroll->approved_hrs / 200);
                        $payroll->payable = round($payable);
                        // Total Payable = Payable + Incentive - Advance + Extra
                        $payroll->total_payable = $payroll->payable + ($payroll->incentive ?? 0) - ($payroll->advance ?? 0) + ($payroll->extra ?? 0);
                    }
                    
                    $payroll->save();
                    $fixedCount++;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "Fixed {$fixedCount} payroll records with missing sal_previous values",
                'fixed_count' => $fixedCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fix sal_previous values: ' . $e->getMessage()], 500);
        }
    }

    public function updatePayable(Request $request, $id)
    {
        try {
            $payable = $request->input('payable');
            $totalPayable = $request->input('total_payable');

            // Find and update the payroll record
            $payroll = \App\Models\Payroll::find($id);
            if (!$payroll) {
                return response()->json(['error' => 'Payroll record not found'], 404);
            }

            if (isset($payable)) {
                $payroll->payable = $payable;
            }
            if (isset($totalPayable)) {
                $payroll->total_payable = $totalPayable;
            }
            
            $payroll->save();

            return response()->json([
                'success' => true,
                'message' => 'Payable amounts updated successfully',
                'payable' => $payroll->payable,
                'total_payable' => $payroll->total_payable
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update payable amounts: ' . $e->getMessage()], 500);
        }
    }

    public function updateExtra(Request $request)
    {
        try {
            $payrollId = $request->input('payroll_id');
            $extraAmount = $request->input('extra');

            // Validate input
            if (!$payrollId || !is_numeric($extraAmount)) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            // Find and update the payroll record
            $payroll = \App\Models\Payroll::find($payrollId);
            if (!$payroll) {
                return response()->json(['error' => 'Payroll record not found'], 404);
            }

            $payroll->extra = floatval($extraAmount);
            
            // Recalculate total payable: payable + incentive - advance + extra
            $totalPayable = ($payroll->payable ?? 0) + ($payroll->incentive ?? 0) - ($payroll->advance ?? 0) + ($payroll->extra ?? 0);
            $payroll->total_payable = round($totalPayable);
            $payroll->save();

            return response()->json([
                'success' => true,
                'message' => 'Extra amount updated successfully',
                'extra' => $payroll->extra,
                'total_payable' => $payroll->total_payable
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update extra amount: ' . $e->getMessage()], 500);
        }
    }

    public function updateIncentive(Request $request)
    {
        try {
            $payrollId = $request->input('payroll_id');
            $incentiveAmount = $request->input('incentive');

            // Validate input
            if (!$payrollId || $incentiveAmount === null) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            // Find and update the payroll record
            $payroll = \App\Models\Payroll::find($payrollId);
            if (!$payroll) {
                return response()->json(['error' => 'Payroll record not found'], 404);
            }

            $payroll->incentive = intval($incentiveAmount); // Store as integer (no decimals)
            
            // Recalculate payable and total_payable with new incentive
            $approvalStatus = $payroll->approval_status ?? 'pending';
            $approvedHrs = $payroll->approved_hrs ?? 0;
            $salaryCurrent = $payroll->salary_current ?? 0;
            
            if ($approvalStatus === 'approved' && $approvedHrs > 0 && $salaryCurrent > 0) {
                // Payable = (Salary × Hours / 200) - incentive is NOT included
                $payroll->payable = ($salaryCurrent * $approvedHrs / 200);
            } else {
                $payroll->payable = 0;
            }
            
            // Recalculate total payable: payable + incentive - advance + extra
            $advance = $payroll->advance ?? 0;
            $extra = $payroll->extra ?? 0;
            $payroll->total_payable = $payroll->payable + $payroll->incentive - $advance + $extra;
            
            $payroll->save();

            return response()->json([
                'success' => true,
                'message' => 'Incentive amount updated successfully',
                'incentive' => $payroll->incentive,
                'payable' => $payroll->payable,
                'total_payable' => $payroll->total_payable
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update incentive amount: ' . $e->getMessage()], 500);
        }
    }

    public function updateAdvance(Request $request)
    {
        try {
            $payrollId = $request->input('payroll_id');
            $advanceAmount = $request->input('advance');

            // Validate input
            if (!$payrollId || $advanceAmount === null) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            // Find and update the payroll record
            $payroll = \App\Models\Payroll::find($payrollId);
            if (!$payroll) {
                return response()->json(['error' => 'Payroll record not found'], 404);
            }

            $payroll->advance = intval($advanceAmount); // Store as integer (no decimals)
            
            // Recalculate total payable: payable - advance + extra
            $payable = $payroll->payable ?? 0;
            $extra = $payroll->extra ?? 0;
            // Total Payable = Payable + Incentive - Advance + Extra
            $payroll->total_payable = $payable + ($payroll->incentive ?? 0) - $payroll->advance + $extra;
            
            $payroll->save();

            return response()->json([
                'success' => true,
                'message' => 'Advance amount updated successfully',
                'advance' => $payroll->advance,
                'total_payable' => $payroll->total_payable
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update advance amount: ' . $e->getMessage()], 500);
        }
    }

    public function updateBank1(Request $request)
    {
        try {
            $payrollId = $request->input('payroll_id');
            $bank1Value = $request->input('bank1');

            // Validate input - bank1 can be a string (account number)
            if (!$payrollId || $bank1Value === null) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            // Find and update the payroll record
            $payroll = \App\Models\Payroll::find($payrollId);
            if (!$payroll) {
                return response()->json(['error' => 'Payroll record not found'], 404);
            }

            $payroll->bank1 = $bank1Value; // Store as string (account number)
            $payroll->save();

            return response()->json([
                'success' => true,
                'message' => 'Bank 1 account updated successfully',
                'bank1' => $payroll->bank1
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update Bank 1 account: ' . $e->getMessage()], 500);
        }
    }

    public function updateBank2(Request $request)
    {
        try {
            $payrollId = $request->input('payroll_id');
            $bank2Value = $request->input('bank2');

            // Validate input - bank2 can be a string (account number)
            if (!$payrollId || $bank2Value === null) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            // Find and update the payroll record
            $payroll = \App\Models\Payroll::find($payrollId);
            if (!$payroll) {
                return response()->json(['error' => 'Payroll record not found'], 404);
            }

            $payroll->bank2 = $bank2Value; // Store as string (account number)
            $payroll->save();

            return response()->json([
                'success' => true,
                'message' => 'Bank 2 account updated successfully',
                'bank2' => $payroll->bank2
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update Bank 2 account: ' . $e->getMessage()], 500);
        }
    }

    public function updateUp(Request $request)
    {
        try {
            $payrollId = $request->input('payroll_id');
            $upValue = $request->input('up');

            // Validate input - up can be a string (text area content)
            if (!$payrollId || $upValue === null) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            // Find and update the payroll record
            $payroll = \App\Models\Payroll::find($payrollId);
            if (!$payroll) {
                return response()->json(['error' => 'Payroll record not found'], 404);
            }

            $payroll->up = $upValue; // Store as text
            $payroll->save();

            return response()->json([
                'success' => true,
                'message' => 'UP field updated successfully',
                'up' => $payroll->up
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update UP field: ' . $e->getMessage()], 500);
        }
    }
    
    public function copyBankDetails(Request $request)
    {
        try {
            $currentMonth = $request->input('current_month');
            $previousMonth = $request->input('previous_month');
            
            // Validate input
            if (!$currentMonth || !$previousMonth) {
                return response()->json(['success' => false, 'message' => 'Current and previous month are required'], 400);
            }
            
            // Get active workspace ID
            $workspaceId = getActiveWorkSpace();
            
            // Get all payroll records from previous month in this workspace
            $prevMonthPayrolls = \App\Models\Payroll::where('workspace_id', $workspaceId)
                ->where('month', $previousMonth)
                ->where('is_enabled', true)
                ->get();
                
            if ($prevMonthPayrolls->isEmpty()) {
                return response()->json([
                    'success' => false, 
                    'message' => "No payroll records found for $previousMonth"
                ], 404);
            }
            
            $updated = 0;
            $created = 0;
            
            // Loop through each previous month payroll
            foreach ($prevMonthPayrolls as $prevPayroll) {
                // Check if there's a corresponding payroll record for the current month
                $currentPayroll = \App\Models\Payroll::where('workspace_id', $workspaceId)
                    ->where('month', $currentMonth)
                    ->where('employee_id', $prevPayroll->employee_id)
                    ->where('is_enabled', true)
                    ->first();
                
                if ($currentPayroll) {
                    // Update existing payroll record
                    $currentPayroll->bank1 = $prevPayroll->bank1;
                    $currentPayroll->bank2 = $prevPayroll->bank2;
                    $currentPayroll->up = $prevPayroll->up;
                    $currentPayroll->save();
                    $updated++;
                } else {
                    // Create a new payroll record for the current month with bank details
                    $newPayroll = $prevPayroll->replicate();
                    $newPayroll->month = $currentMonth;
                    
                    // Only copy bank related data, not salary/incentive data
                    $newPayroll->sal_previous = 0;
                    $newPayroll->increment = 0;
                    $newPayroll->salary_current = 0;
                    $newPayroll->productive_hrs = 0;
                    $newPayroll->approved_hrs = 0;
                    $newPayroll->etc_hours = 0;
                    $newPayroll->atc_hours = 0;
                    $newPayroll->incentive = 0;
                    $newPayroll->payable = 0;
                    $newPayroll->advance = 0;
                    $newPayroll->extra = 0;
                    $newPayroll->total_payable = 0;
                    $newPayroll->payment_done = false;
                    
                    // But keep bank details
                    // bank1, bank2, and up are already copied from replicate()
                    
                    $newPayroll->save();
                    $created++;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "Bank details copied successfully! Updated: $updated records, Created: $created new records.",
                'updated' => $updated,
                'created' => $created
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to copy bank details: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to copy bank details: ' . $e->getMessage()], 500);
        }
    }

    public function updateSalaryData(Request $request)
    {
        try {
            $payrollId = $request->input('payroll_id');
            $salPrevious = $request->input('sal_previous', 0);
            $increment = $request->input('increment', 0);

            // Validate input
            if (!$payrollId) {
                return response()->json(['error' => 'Invalid payroll ID'], 400);
            }

            // Find and update the payroll record
            $payroll = \App\Models\Payroll::find($payrollId);
            if (!$payroll) {
                return response()->json(['error' => 'Payroll record not found'], 404);
            }

            // Update only salary-related fields, preserve TeamLogger data
            $payroll->sal_previous = floatval($salPrevious);
            $payroll->increment = floatval($increment);
            $payroll->salary_current = $payroll->sal_previous + $payroll->increment;
            
            // Recalculate payable based on current approval status and approved hours
            $approvalStatus = $payroll->approval_status ?? 'pending';
            $approvedHrs = $payroll->approved_hrs ?? 0;
            
            if ($approvalStatus === 'approved' && $approvedHrs > 0 && $payroll->salary_current > 0) {
                // Payable = (Salary × Hours / 200) - incentive is NOT included
                $payroll->payable = ($payroll->salary_current * $approvedHrs / 200);
            } else {
                $payroll->payable = 0;
            }
            
            // Recalculate total payable: payable + incentive - advance + extra
            $advance = $payroll->advance ?? 0;
            $extra = $payroll->extra ?? 0;
            $payroll->total_payable = $payroll->payable + ($payroll->incentive ?? 0) - $advance + $extra;
            
            $payroll->save();

            return response()->json([
                'success' => true,
                'message' => 'Salary data updated successfully',
                'sal_previous' => $payroll->sal_previous,
                'increment' => $payroll->increment,
                'salary_current' => $payroll->salary_current,
                'payable' => $payroll->payable,
                'total_payable' => $payroll->total_payable
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update salary data: ' . $e->getMessage()], 500);
        }
    }

    // Method to refresh TeamLogger data for a specific employee and month
    public function refreshTeamLoggerData(Request $request)
    {
        try {
            $workspaceId = getActiveWorkSpace();
            $employeeId = $request->get('employee_id');
            $month = $request->get('month');
            
            if (!$employeeId || !$month) {
                return response()->json(['error' => 'Employee ID and month are required'], 400);
            }
            
            // Find the payroll record
            $payroll = Payroll::where('workspace_id', $workspaceId)
                ->where('employee_id', $employeeId)
                ->where('month', $month)
                ->first();
                
            if (!$payroll) {
                return response()->json(['error' => 'Payroll record not found'], 404);
            }
            
            // Get fresh TeamLogger data
            $etcAtcData = $this->getEmployeeETCAndATC($payroll->email_address, $month);
            $teamLoggerData = $this->getEmployeeTeamLoggerData($payroll->email_address, $month);
            
            // Update only TeamLogger-related fields
            $payroll->update([
                'productive_hrs' => $teamLoggerData['hours'],
                'etc_hours' => $etcAtcData['etc'],
                'atc_hours' => $etcAtcData['atc'],
            ]);
            
            // Recalculate payable if needed
            $approvalStatus = $payroll->approval_status ?? 'pending';
            $approvedHrs = $payroll->approved_hrs ?? $teamLoggerData['hours'];
            
            if ($approvalStatus === 'approved' && $approvedHrs > 0 && $payroll->salary_current > 0) {
                // Payable = (Salary × Hours / 200) - incentive is NOT included
                $payable = ($payroll->salary_current * $approvedHrs / 200);
                $advance = $payroll->advance ?? 0;
                $extra = $payroll->extra ?? 0;
                // Total Payable = Payable + Incentive - Advance + Extra
                $totalPayable = $payable + ($payroll->incentive ?? 0) - $advance + $extra;
                
                $payroll->update([
                    'payable' => $payable,
                    'total_payable' => $totalPayable,
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'TeamLogger data refreshed successfully',
                'data' => [
                    'productive_hrs' => $payroll->productive_hrs,
                    'etc_hours' => $payroll->etc_hours,
                    'atc_hours' => $payroll->atc_hours,
                    'payable' => $payroll->payable,
                    'total_payable' => $payroll->total_payable,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Method to fix corrupted payroll data
    public function fixCorruptedData(Request $request)
    {
        try {
            $workspaceId = getActiveWorkSpace();
            $employeeId = $request->get('employee_id');
            $month = $request->get('month');
            
            if (!$employeeId || !$month) {
                return response()->json(['error' => 'Employee ID and month are required'], 400);
            }
            
            // Find the payroll record
            $payroll = Payroll::where('workspace_id', $workspaceId)
                ->where('employee_id', $employeeId)
                ->where('month', $month)
                ->first();
                
            if (!$payroll) {
                return response()->json(['error' => 'Payroll record not found'], 404);
            }
            
            // Fix sal_previous if it's 0 and we can get it from previous month
            if (!$payroll->sal_previous || $payroll->sal_previous == 0) {
                $previousMonth = $this->getPreviousMonth($month);
                $previousMonthPayroll = Payroll::where('workspace_id', $workspaceId)
                    ->where('employee_id', $employeeId)
                    ->where('month', $previousMonth)
                    ->first();
                    
                if ($previousMonthPayroll && $previousMonthPayroll->salary_current > 0) {
                    $payroll->sal_previous = $previousMonthPayroll->salary_current;
                    $payroll->salary_current = $payroll->sal_previous + $payroll->increment;
                }
            }
            
            // Fix salary_current if it's 0
            if (!$payroll->salary_current || $payroll->salary_current == 0) {
                $payroll->salary_current = $payroll->sal_previous + $payroll->increment;
            }
            
            // Recalculate payable if status is approved but payable is 0
            if ($payroll->approval_status === 'approved' && $payroll->approved_hrs > 0 && $payroll->salary_current > 0) {
                // Payable = (Salary × Hours / 200) - incentive is NOT included
                $payable = ($payroll->salary_current * $payroll->approved_hrs / 200);
                $advance = $payroll->advance ?? 0;
                $extra = $payroll->extra ?? 0;
                // Total Payable = Payable + Incentive - Advance + Extra
                $totalPayable = $payable + ($payroll->incentive ?? 0) - $advance + $extra;
                
                $payroll->payable = $payable;
                $payroll->total_payable = $totalPayable;
            }
            
            $payroll->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Data fixed successfully',
                'data' => [
                    'sal_previous' => $payroll->sal_previous,
                    'increment' => $payroll->increment,
                    'salary_current' => $payroll->salary_current,
                    'payable' => $payroll->payable,
                    'total_payable' => $payroll->total_payable,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Debug method to check payroll data integrity
    public function debugPayrollData(Request $request)
    {
        try {
            $workspaceId = getActiveWorkSpace();
            $employeeId = $request->get('employee_id');
            $selectedMonth = $request->get('month', 'September 2025');
            
            if (!$employeeId) {
                return response()->json(['error' => 'Employee ID required'], 400);
            }
            
            // Get all payroll records for this employee
            $allPayrolls = Payroll::where('workspace_id', $workspaceId)
                ->where('employee_id', $employeeId)
                ->orderBy('month', 'asc')
                ->get();
                
            // Get previous month
            $previousMonth = $this->getPreviousMonth($selectedMonth);
            
            $debugData = [
                'employee_id' => $employeeId,
                'selected_month' => $selectedMonth,
                'previous_month' => $previousMonth,
                'all_payrolls' => $allPayrolls->map(function($p) {
                    return [
                        'id' => $p->id,
                        'month' => $p->month,
                        'sal_previous' => $p->sal_previous,
                        'increment' => $p->increment,
                        'salary_current' => $p->salary_current,
                        'productive_hrs' => $p->productive_hrs,
                        'etc_hours' => $p->etc_hours,
                        'atc_hours' => $p->atc_hours,
                        'is_enabled' => $p->is_enabled,
                        'created_at' => $p->created_at,
                        'updated_at' => $p->updated_at
                    ];
                }),
                'current_month_payroll' => $allPayrolls->where('month', $selectedMonth)->first(),
                'previous_month_payroll' => $allPayrolls->where('month', $previousMonth)->first(),
            ];
            
            return response()->json([
                'success' => true,
                'debug_data' => $debugData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function printPayrollPDF(Request $request)
    {
        try {
            $selectedMonth = $request->get('month', 'August 2025');
            $workspaceId = getActiveWorkSpace();
            
            // Debug: Log the request
            Log::info('Print PDF request for month: ' . $selectedMonth . ', workspace: ' . $workspaceId);
            
            // Get payroll data based on current filters
            $searchName = $request->get('searchName', '');
            $searchDept = $request->get('searchDept', '');
            $searchEmail = $request->get('searchEmail', '');
            $searchPaymentStatus = $request->get('searchPaymentStatus', '');
            
            // Get all payrolls for the selected month
            $payrolls = Payroll::where('workspace_id', $workspaceId)
                ->where('month', $selectedMonth)
                ->where('is_enabled', true)
                ->when($searchName, function($query) use ($searchName) {
                    return $query->where('name', 'like', '%' . $searchName . '%');
                })
                ->when($searchDept, function($query) use ($searchDept) {
                    return $query->where('department', 'like', '%' . $searchDept . '%');
                })
                ->when($searchEmail, function($query) use ($searchEmail) {
                    return $query->where('email_address', 'like', '%' . $searchEmail . '%');
                })
                ->when($searchPaymentStatus, function($query) use ($searchPaymentStatus) {
                    if ($searchPaymentStatus === 'Done') {
                        return $query->where('payment_done', true);
                    } elseif ($searchPaymentStatus === 'Pending') {
                        return $query->where('payment_done', false);
                    }
                    return $query;
                })
                ->orderBy('name', 'asc')
                ->get();

            Log::info('Found ' . $payrolls->count() . ' payroll records');

            // Prepare data for PDF with only the required fields
            $data = [
                'payrolls' => $payrolls,
                'month' => $selectedMonth,
                'generated_at' => now()->format('Y-m-d H:i:s')
            ];

            // For now, let's return the view directly to see if the template works
            return view('payroll.print-pdf', $data)->with('print_mode', true);

        } catch (\Exception $e) {
            Log::error('Print payroll error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            // $selectedMonth = $request->get('month', 'August 2025');
             $selectedMonth = $request->get('month', Carbon::now()->format('F Y'));
            $workspaceId = getActiveWorkSpace();

                 $employees = User::where('workspace_id', $workspaceId)
            ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->select(
                'users.id as employee_id',
                'users.name',
                'users.email as email_address',
                'departments.name as department'
            )
            ->whereNotNull('employees.user_id') // Only get users who are employees
            ->orderBy('users.name', 'asc')
            ->get();

            // If frontend exportData is provided, use it
            $frontendData = $request->get('exportData');
            if ($frontendData) {
                $payrolls = collect(json_decode($frontendData, true));
            } else {
                // Fallback to backend data
                $searchName = $request->get('searchName', '');
                $searchDept = $request->get('searchDept', '');
                $searchEmail = $request->get('searchEmail', '');
                $searchPaymentStatus = $request->get('searchPaymentStatus', '');
                $payrolls = Payroll::where('workspace_id', $workspaceId)
                    ->where('month', $selectedMonth)
                    ->where('is_enabled', true)
                    ->when($searchName, function($query) use ($searchName) {
                        return $query->where('name', 'like', '%' . $searchName . '%');
                    })
                    ->when($searchDept, function($query) use ($searchDept) {
                        return $query->where('department', 'like', '%' . $searchDept . '%');
                    })
                    ->when($searchEmail, function($query) use ($searchEmail) {
                        return $query->where('email_address', 'like', '%' . $searchEmail . '%');
                    })
                    ->when($searchPaymentStatus, function($query) use ($searchPaymentStatus) {
                        if ($searchPaymentStatus === 'Done') {
                            return $query->where('payment_done', true);
                        } elseif ($searchPaymentStatus === 'Pending') {
                            return $query->where('payment_done', false);
                        }
                        return $query;
                    })
                    ->orderBy('name', 'asc')
                    ->get();

                       // Get all archived employees (from any month) to exclude from main payroll list
       $archivedEmployeeIds = Payroll::where('workspace_id', $workspaceId)
            ->where(function($query) {
                $query->where('is_enabled', false)
                      ->orWhere('is_contractual', true);
            })
            ->pluck('employee_id')
            ->unique()
            ->toArray();

             $payrollRecords = Payroll::where('workspace_id', $workspaceId)
            ->where('month', $selectedMonth)
            ->get()
            ->groupBy('employee_id');

                   // Get previous month name for sal_previous calculation
        $previousMonth = $this->getPreviousMonth($selectedMonth);
        
            $previousMonthRecords = Payroll::where('workspace_id', $workspaceId)
            ->where('month', $previousMonth)
            ->get()
            ->groupBy('employee_id');

        

            // filter employees 
              $payrolls = $employees->filter(function($employee) use ($payrollRecords, $selectedMonth, $archivedEmployeeIds) {
            // Exclude archived/contractual employees from main payroll list
            if (in_array($employee->employee_id, $archivedEmployeeIds)) {
                return false;
            }
            
            $employeePayrolls = $payrollRecords->get($employee->employee_id, collect());
            
            // Find payroll for selected month
            $currentMonthPayroll = $employeePayrolls->first();
            
            // Only include employee if they have no payroll record OR have an enabled payroll record for this month
            return !$currentMonthPayroll || ($currentMonthPayroll && $currentMonthPayroll->is_enabled);
        })->map(function($employee) use ($payrollRecords, $previousMonthRecords, $selectedMonth, $previousMonth) {
            $employeePayrolls = $payrollRecords->get($employee->employee_id, collect());
            $employeePreviousPayrolls = $previousMonthRecords->get($employee->employee_id, collect());
            
            // Find payroll for selected month
            $currentMonthPayroll = $employeePayrolls->first();
            
            // Find payroll for previous month (for sal_previous)
            $previousMonthPayroll = $employeePreviousPayrolls->first();
            
            // Get ETC and ATC data for the employee
            $etcAtcData = $this->getEmployeeETCAndATC($employee->email_address, $selectedMonth);
            
            // Get TeamLogger data for the employee
            $teamLoggerData = $this->getEmployeeTeamLoggerData($employee->email_address, $selectedMonth);
            
            // Get overdue count for the employee
            $overdueCount = $this->getEmployeeOverdueCount($employee->email_address, $selectedMonth);
            
            if ($currentMonthPayroll) {
                // Employee has payroll record for selected month
                $approvedHrs = $currentMonthPayroll->approved_hrs ?? $teamLoggerData['hours'];
                $approvalStatus = $currentMonthPayroll->approval_status ?? 'pending';
                
                // Calculate payable based on approval status and approved hours
                $payable = 0;
                $totalPayable = 0;
                
                if ($approvalStatus === 'approved' && $approvedHrs > 0 && $currentMonthPayroll->salary_current) {
                    // Payable = (Salary × Hours / 200) - incentive is NOT included
                    $payable = ($currentMonthPayroll->salary_current * $approvedHrs / 200);
                    // Total Payable = Payable + Incentive - Advance + Extra
                    $totalPayable = $payable + ($currentMonthPayroll->incentive ?? 0) - ($currentMonthPayroll->advance ?? 0) + ($currentMonthPayroll->extra ?? 0);
                }
                
                // Use stored sal_previous from database - do NOT dynamically calculate from other months
                // This prevents cross-month data contamination
                $salPrevious = $currentMonthPayroll->sal_previous;
                
                return (object) [
                    'employee_id' => $employee->employee_id,
                    'name' => $employee->name,
                    'email_address' => $employee->email_address,
                    'department' => $employee->department,
                    'id' => $currentMonthPayroll->id,
                    'month' => $currentMonthPayroll->month,
                    'sal_previous' => $salPrevious,
                    'increment' => $currentMonthPayroll->increment,
                    'salary_current' => $currentMonthPayroll->salary_current,
                    'productive_hrs' => $teamLoggerData['hours'], // Use TeamLogger data
                    'approved_hrs' => $approvedHrs,
                    'approval_status' => $approvalStatus,
                    'total_hours' => $teamLoggerData['total_hours'] ?? 0,
                    'idle_hours' => $teamLoggerData['idle_hours'] ?? 0,
                    'etc_hours' => $etcAtcData['etc'],
                    'atc_hours' => $etcAtcData['atc'],
                    'overdue_count' => $overdueCount,
                    'incentive' => $currentMonthPayroll->incentive,
                    'payable' => round($payable),
                    'advance' => $currentMonthPayroll->advance,
                    'extra' => $currentMonthPayroll->extra,
                    'total_payable' => round($totalPayable),
                    'bank1' => $currentMonthPayroll->bank1,
                    'bank2' => $currentMonthPayroll->bank2,
                    'up' => $currentMonthPayroll->up,
                    'payment_done' => $currentMonthPayroll->payment_done,
                    'is_enabled' => $currentMonthPayroll->is_enabled ?? true,
                    'created_at' => $currentMonthPayroll->created_at
                ];
            } else {
                // Employee doesn't have payroll record for selected month
                // Get salary data from previous month to auto-populate in current month
                $salPrevious = null;
                $salaryCurrent = null;
                
                // Find payroll for previous month to get salary data
                if ($previousMonthPayroll) {
                    $salPrevious = $previousMonthPayroll->salary_current ?? 0;
                    $salaryCurrent = $salPrevious; // Set current salary same as previous month's salary
                }
                
                return (object) [
                    'employee_id' => $employee->employee_id,
                    'name' => $employee->name,
                    'email_address' => $employee->email_address,
                    'department' => $employee->department,
                    'id' => null,
                    'month' => $selectedMonth,
                    'sal_previous' => $salPrevious,
                    'increment' => 0, // Default increment to 0
                    'salary_current' => $salaryCurrent,
                    'productive_hrs' => $teamLoggerData['hours'], // Use TeamLogger data
                    'approved_hrs' => $teamLoggerData['hours'], // Default to TeamLogger hours
                    'approval_status' => 'pending', // Default status
                    'total_hours' => $teamLoggerData['total_hours'] ?? 0,
                    'idle_hours' => $teamLoggerData['idle_hours'] ?? 0,
                    'etc_hours' => $etcAtcData['etc'],
                    'atc_hours' => $etcAtcData['atc'],
                    'overdue_count' => $overdueCount,
                    'incentive' => null,
                    'payable' => 0, // Default to 0 since no approval and salary data
                    'advance' => null,
                    'extra' => null,
                    'total_payable' => 0, // Default to 0
                    'bank1' => null,
                    'bank2' => null,
                    'up' => null,
                    'payment_done' => false,
                    'is_enabled' => true, // Default to enabled for new records
                    'created_at' => null
                ];
            }
        });
            
            }

            // Create Excel file with the frontend or backend data
            return Excel::download(new \App\Exports\PayrollExport($payrolls, $selectedMonth),
                'payroll_' . str_replace(' ', '_', $selectedMonth) . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx');

        } catch (\Exception $e) {
            Log::error('Export Excel error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating Excel file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test method to verify PDF generation works
     * You can access this via: /payroll/test-pdf/{payroll_id}
     */
    public function testPdfGeneration($id)
    {
        try {
            $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
            $payroll = Payroll::where('workspace_id', $workspaceId)->findOrFail($id);
            
            // Generate PDF
            $pdfPath = $this->generateSalarySlipPDF($payroll);
            
            if (file_exists($pdfPath)) {
                $fileExtension = pathinfo($pdfPath, PATHINFO_EXTENSION);
                
                // Return the file for download
                return response()->download($pdfPath, "test_salary_slip_{$payroll->month}.{$fileExtension}")->deleteFileAfterSend(true);
            } else {
                return response()->json(['error' => 'PDF generation failed'], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Test PDF generation error: ' . $e->getMessage());
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
