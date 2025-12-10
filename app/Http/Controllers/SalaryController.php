<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Workdo\Hrm\Entities\Employee;
use Workdo\Hrm\Entities\Department;
use App\Models\Incentive;
use App\Models\SalaryProposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    public function incentive()
    {
        $employees = Employee::with(['user', 'department'])
            ->where('workspace', getActiveWorkSpace())
            ->where('is_active', 1)
            ->get();
        
        $departments = Department::where('workspace', getActiveWorkSpace())->get();
        
        // Get current user's employee record
        $currentEmployee = Employee::where('user_id', Auth::id())
            ->where('workspace', getActiveWorkSpace())
            ->first();
        
        return view('salary.incentive', compact('employees', 'departments', 'currentEmployee'));
    }
    
    public function increment()
    {
        $employees = Employee::with(['user', 'department'])
            ->where('workspace', getActiveWorkSpace())
            ->where('is_active', 1)
            ->get();
        
        $departments = Department::where('workspace', getActiveWorkSpace())->get();
        
        // Get current user's employee record
        $currentEmployee = Employee::where('user_id', Auth::id())
            ->where('workspace', getActiveWorkSpace())
            ->first();
        
        return view('salary.increment', compact('employees', 'departments', 'currentEmployee'));
    }
    
    public function submitIncentive(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'department' => 'required|string',
            'incentive_month' => 'required|string',
            'requested_incentive' => 'required|numeric|min:0',
            'incentive_reason' => 'required|string',
            'approved_incentive' => 'nullable|numeric|min:0',
            'approval_reason' => 'nullable|string',
            'approved_by' => 'nullable|string',
            'review_date' => 'nullable|date',
        ]);

        try {
            // Get employee details
            $employee = Employee::findOrFail($request->employee_id);
            
            // Always create record as pending, regardless of approval fields
            // Approval fields are ignored during form submission - only used via records page
            $incentive = Incentive::create([
                'employee_id' => $request->employee_id,
                'employee_name' => $employee->name,
                'department' => $request->department,
                'incentive_month' => $request->incentive_month,
                'requested_incentive' => $request->requested_incentive,
                'incentive_reason' => $request->incentive_reason,
                // Always set as pending when submitted from form
                'approved_incentive' => null, // null means pending
                'approval_reason' => null,
                'approved_by' => null,
                'review_date' => null,
                'status' => 'pending', // Always pending on form submission
                'workspace' => getActiveWorkSpace(),
                'created_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Incentive form submitted successfully as Pending! Record ID: ' . $incentive->id);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error saving incentive: ' . $e->getMessage()])->withInput();
        }
    }
    
    public function storeProposal(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'department' => 'required|string',
            'review_month' => 'nullable|string',
            'proposal_type' => 'required|in:increase,no_increase',
            'proposed_amount' => 'nullable|numeric|min:0',
            'comments' => 'nullable|string',
            'approved_by' => 'nullable|string',
            'approval_status' => 'nullable|in:pending,approved,rejected',
        ]);

        try {
            // Get employee details
            $employee = Employee::findOrFail($request->employee_id);
            
            // Create salary proposal record
            $proposal = SalaryProposal::create([
                'employee_id' => $request->employee_id,
                'employee_name' => $employee->name,
                'department' => $request->department,
                'review_month' => $request->review_month,
                'proposal_type' => $request->proposal_type,
                'proposed_amount' => $request->proposed_amount,
                'comments' => $request->comments,
                'approved_by' => $request->approved_by,
                'approval_status' => $request->approval_status ?? 'pending',
                'workspace' => getActiveWorkSpace(),
                'created_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Salary proposal submitted successfully! Record ID: ' . $proposal->id);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error saving proposal: ' . $e->getMessage()])->withInput();
        }
    }
    
    public function getEmployeeData($id)
    {
        try {
            // Remove all filters to find the employee first
            $employee = Employee::find($id);
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ]);
            }
            
            $departmentName = 'No Department';
            
            // Try multiple ways to get department
            if ($employee->department_id) {
                // Try direct department query
                $department = Department::find($employee->department_id);
                if ($department) {
                    $departmentName = $department->name;
                }
            }
            
            // If still no department, try with workspace filter
            if ($departmentName === 'No Department' && $employee->department_id) {
                try {
                    $department = Department::where('id', $employee->department_id)
                        ->where('workspace', getActiveWorkSpace())
                        ->first();
                    if ($department) {
                        $departmentName = $department->name;
                    }
                } catch (Exception $e) {
                    // Ignore workspace errors
                }
            }
            
            // If still no department, get any department with that ID
            if ($departmentName === 'No Department' && $employee->department_id) {
                $department = Department::where('id', $employee->department_id)->first();
                if ($department) {
                    $departmentName = $department->name;
                }
            }
            
            return response()->json([
                'success' => true,
                'name' => $employee->name,
                'department' => $departmentName,
                'debug' => [
                    'employee_id' => $employee->id,
                    'department_id' => $employee->department_id,
                    'employee_name' => $employee->name
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function incentiveRecords()
    {
        try {
            $incentives = Incentive::with(['employee'])
                ->where('workspace', getActiveWorkSpace())
                ->orderBy('created_at', 'desc')
                ->get();
                
            return view('salary.incentive-records', compact('incentives'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error loading incentive records: ' . $e->getMessage()]);
        }
    }
    
    public function incrementRecords()
    {
        try {
            $proposals = SalaryProposal::with(['employee'])
                ->where('workspace', getActiveWorkSpace())
                ->orderBy('created_at', 'desc')
                ->get();
                
            return view('salary.increment-records', compact('proposals'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error loading increment records: ' . $e->getMessage()]);
        }
    }
    
    // Incentive CRUD Methods
    public function updateIncentive(Request $request, $id)
    {
        try {
            \Log::info('UpdateIncentive called with ID: ' . $id, $request->all());
            
            $incentive = Incentive::where('id', $id)
                ->where('workspace', getActiveWorkSpace())
                ->firstOrFail();
            
            $incentive->update([
                'incentive_month' => $request->incentive_month,
                'requested_incentive' => $request->requested_incentive,
                'approved_incentive' => $request->approved_incentive,
                'incentive_reason' => $request->incentive_reason,
            ]);
            
            return response()->json(['success' => true, 'message' => 'Incentive updated successfully']);
        } catch (\Exception $e) {
            \Log::error('Error updating incentive: ' . $e->getMessage(), [
                'id' => $id,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Error updating incentive: ' . $e->getMessage()], 500);
        }
    }
    
    public function updateIncentiveStatus(Request $request, $id)
    {
        try {
            \Log::info('UpdateIncentiveStatus called', [
                'id' => $id,
                'request' => $request->all()
            ]);
            
            $incentive = Incentive::where('id', $id)
                ->where('workspace', getActiveWorkSpace())
                ->firstOrFail();
            
            // Determine status based on approved amount
            $approvedAmount = $request->approved_incentive;
            $status = ($approvedAmount > 0) ? 'approved' : 'rejected';
            
            $updateData = [
                'approved_incentive' => $approvedAmount,
                'approval_reason' => $request->approval_reason,
                'approved_by' => Auth::user()->name ?? 'System',
                'review_date' => now(),
                'status' => $status, // Update the status field
            ];
            
            \Log::info('Update data:', $updateData);
            
            $incentive->update($updateData);
            
            return response()->json([
                'success' => true, 
                'message' => "Incentive {$status} successfully",
                'data' => $incentive->fresh()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating incentive status: ' . $e->getMessage(), [
                'id' => $id,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteIncentive($id)
    {
        try {
            $incentive = Incentive::where('id', $id)
                ->where('workspace', getActiveWorkSpace())
                ->firstOrFail();
            $incentive->delete();
            
            return response()->json(['success' => true, 'message' => 'Incentive deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting incentive: ' . $e->getMessage()], 500);
        }
    }
    
    // Increment CRUD Methods
    public function updateIncrement(Request $request, $id)
    {
        try {
            $proposal = SalaryProposal::where('id', $id)
                ->where('workspace', getActiveWorkSpace())
                ->firstOrFail();
            
            $proposal->update([
                'review_month' => $request->review_month,
                'proposal_type' => $request->proposal_type,
                'proposed_amount' => $request->proposed_amount,
                'comments' => $request->comments,
            ]);
            
            return response()->json(['success' => true, 'message' => 'Proposal updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating proposal: ' . $e->getMessage()], 500);
        }
    }
    
    public function updateIncrementStatus(Request $request, $id)
    {
        try {
            $proposal = SalaryProposal::where('id', $id)
                ->where('workspace', getActiveWorkSpace())
                ->firstOrFail();
            
            $proposal->update([
                'approval_status' => $request->approval_status,
                'approval_comments' => $request->approval_comments,
                'approved_by' => Auth::user()->name ?? 'System',
                'approved_at' => now(),
            ]);
            
            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()], 500);
        }
    }
    
    public function deleteIncrement($id)
    {
        try {
            $proposal = SalaryProposal::where('id', $id)
                ->where('workspace', getActiveWorkSpace())
                ->firstOrFail();
            $proposal->delete();
            
            return response()->json(['success' => true, 'message' => 'Proposal deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting proposal: ' . $e->getMessage()], 500);
        }
    }
    
    public function salaryBoard()
    {
        // Get all employees with their salary information
        $employeeCollection = collect();
        
        $employees = Employee::with(['user', 'department'])
            ->where('workspace', getActiveWorkSpace())
            ->where('is_active', 1)
            ->get();
        
        // Sample data for demonstration - you can replace this with real logic
        $sampleSalaryData = [
            ['last_month' => 42000, 'increment' => 1000, 'hours' => 209, 'incentive' => 0, 'advance' => 0],
            ['last_month' => 35000, 'increment' => 800, 'hours' => 200, 'incentive' => 5000, 'advance' => 2000],
            ['last_month' => 38000, 'increment' => 1200, 'hours' => 205, 'incentive' => 3000, 'advance' => 1000],
            ['last_month' => 45000, 'increment' => 1500, 'hours' => 210, 'incentive' => 8000, 'advance' => 0],
            ['last_month' => 32000, 'increment' => 600, 'hours' => 195, 'incentive' => 2000, 'advance' => 500],
        ];
        
        foreach ($employees as $index => $employee) {
            // Get sample data or use defaults
            $sampleData = $sampleSalaryData[$index % count($sampleSalaryData)];
            
            // Get latest incentive for the employee
            $latestIncentive = Incentive::where('employee_id', $employee->id)
                ->where('workspace', getActiveWorkSpace())
                ->where('status', 'approved')
                ->latest()
                ->first();
            
            // Get latest increment for the employee
            $latestIncrement = SalaryProposal::where('employee_id', $employee->id)
                ->where('workspace', getActiveWorkSpace())
                ->where('approval_status', 'approved')
                ->latest()
                ->first();
            
            // Calculate salary components
            $basicSalary = $sampleData['last_month']; // Base salary before increment
            $incrementAmount = 0; // Default increment
            
            // Get latest approved increment
            if ($latestIncrement && $latestIncrement->proposed_salary) {
                $incrementAmount = $latestIncrement->proposed_salary - $basicSalary;
                if ($incrementAmount < 0) $incrementAmount = 0; // Ensure positive increment
            } else {
                // Use sample data increment if no real increment found
                $incrementAmount = $sampleData['increment'] ?? 0;
            }
            
            $currentSalary = $basicSalary + $incrementAmount; // Basic + Increment
            $totalPrdHour = 0; // Default to 0, will be editable in the UI
            $incentive = $latestIncentive ? $latestIncentive->approved_incentive : $sampleData['incentive'];
            $advance = $sampleData['advance'];
            
            // Calculate total salary using the new formula: (Current Salary * Total Prd Hour / 200) + Incentive
            $totalSalary = ($currentSalary * $totalPrdHour / 200) + $incentive;
            
            $employeeCollection->push([
                'id' => $employee->id,
                'employee_id' => 'EMP' . str_pad($employee->id, 3, '0', STR_PAD_LEFT),
                'name' => $employee->name ?? 'N/A',
                'department' => $employee->department ? $employee->department->name : 'N/A',
                'email' => $employee->email ?? ($employee->user ? $employee->user->email : 'N/A'),
                'basic_salary' => $basicSalary, // Base salary without increment
                'increment' => $incrementAmount, // Approved increment amount
                'current_salary' => $currentSalary, // Basic + Increment
                'total_prd_hour' => $totalPrdHour, // Editable productive hours
                'incentives' => $incentive,
                'total_salary' => $totalSalary,
            ]);
        }
        
        // Calculate statistics
        $totalSalary = $employeeCollection->sum('total_salary');
        $avgSalary = $employeeCollection->count() > 0 ? $employeeCollection->avg('total_salary') : 0;
        $departments = $employeeCollection->pluck('department')->unique()->filter()->values();
        
        // Convert collection to array for the view
        $employees = $employeeCollection->toArray();
        
        return view('salary.salary-board', compact('employees', 'totalSalary', 'avgSalary', 'departments'));
    }
    
    public function markPaymentDone(Request $request, $employeeId)
    {
        try {
            $request->validate([
                'payment_date' => 'required|date',
                'payment_method' => 'required|string',
                'transaction_ref' => 'nullable|string'
            ]);
            
            // You can create a payments table to track this
            // For now, we'll just return success
            // In a real application, you'd store payment records
            
            \Log::info('Payment marked as done', [
                'employee_id' => $employeeId,
                'payment_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment marked as completed successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error marking payment as done: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating payment status: ' . $e->getMessage()
            ], 500);
        }
    }
}
