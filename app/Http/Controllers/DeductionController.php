<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DeductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if user is admin or special email for DataTable view
        if (Auth::user()->type == 'super admin' || 
            Auth::user()->email == 'president@5core.com' || 
            Auth::user()->email == 'tech-support@5core.com'||
            Auth::user()->email == 'support@5core.com' ||
            Auth::user()->email == 'mgr-content@5core.com' || 
            Auth::user()->email == 'inventory@5core.com') {
            
            // Get ALL deductions for admin
            $deductions = Deduction::with(['giver', 'receiver'])
                                 ->orderBy('created_at', 'desc')
                                 ->get();
            
            // Pass to admin view
            return view('deductions.admin_index', compact('deductions'));
        }
        
        // Regular employees see only deductions they have received
        $deductions = Deduction::with(['giver', 'receiver'])
                              ->where('receiver_id', Auth::id())
                              ->orderBy('created_at', 'desc')
                              ->get();
        
        return view('deductions.index', compact('deductions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user has permission to apply deductions
        if (!in_array(Auth::user()->email, ['president@5core.com', 'tech-support@5core.com', 'inventory@5core.com'])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to apply deductions.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'deduction_team_member_id' => 'required|exists:users,id',
            'deduction_amount' => 'required|numeric|min:0.01',
            'deduction_date' => 'required|date',
            'deduction_description' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get the receiver user
            $receiver = User::find($request->input('deduction_team_member_id'));
            if (!$receiver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected team member not found.'
                ], 404);
            }

            // Prepare deduction data
            $deductionData = [
                'giver_id' => Auth::id(),
                'receiver_id' => $request->input('deduction_team_member_id'),
                'employee_id' => $request->input('deduction_team_member_id'),
                'amount' => $request->input('deduction_amount'),
                'deduction_date' => $request->input('deduction_date'),
                'description' => $request->input('deduction_description'),
                'status' => 'active',
                'workspace_id' => 1,
                'created_by' => Auth::id(),
                // Add fields for backward compatibility
                'employee_name' => $receiver->name,
                'department' => $receiver->department ?? 'General',
                'deduction_month' => date('Y-m', strtotime($request->input('deduction_date'))),
                'requested_deduction' => $request->input('deduction_amount'),
                'deduction_reason' => $request->input('deduction_description'),
                'approved_deduction' => $request->input('deduction_amount'),
                'approval_reason' => 'Auto-approved by ' . Auth::user()->name,
                'approved_by' => Auth::id(),
                'review_date' => now(),
                'workspace' => 'default'
            ];

            $deduction = Deduction::create($deductionData);

            return response()->json([
                'success' => true,
                'message' => 'Deduction applied successfully!',
                'data' => $deduction
            ]);

        } catch (\Exception $e) {
            \Log::error('Deduction creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $deduction = Deduction::with(['giver', 'receiver'])->find($id);
        
        if (!$deduction) {
            return response()->json(['error' => 'Deduction not found.'], 404);
        }

        // Check if user can view this deduction
        if (Auth::user()->type != 'super admin' && 
            !in_array(Auth::user()->email, ['president@5core.com', 'tech-support@5core.com', 'inventory@5core.com']) &&
            $deduction->receiver_id != Auth::id()) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }
        
        return view('deductions.show', compact('deduction'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Check if user has permission to delete deductions
        if (!in_array(Auth::user()->email, ['president@5core.com', 'tech-support@5core.com', 'inventory@5core.com']) &&
            Auth::user()->type != 'super admin') {
            return response()->json(['error' => __('Permission denied.')], 403);
        }

        try {
            $deduction = Deduction::find($id);
            
            if (!$deduction) {
                return response()->json(['error' => __('Deduction not found.')], 404);
            }

            $deduction->delete();
            
            return response()->json(['success' => __('Deduction deleted successfully.')]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('Something went wrong.')], 500);
        }
    }

    /**
     * Check for deductions via AJAX
     */
    public function checkDeductions()
    {
        try {
            $user = Auth::user();
            
            // Get ALL active deductions for the current user (for header display)
            $allActiveDeductions = Deduction::where('receiver_id', $user->id)
                                          ->where('status', 'active')
                                          ->get();
            
            // Get deductions created in last 24 hours for popup
            $newDeductions = Deduction::where('receiver_id', $user->id)
                                    ->where('status', 'active')
                                    ->where('created_at', '>', now()->subDay())
                                    ->get();
            
            $totalAmount = $allActiveDeductions->sum('amount');
            $hasNewDeductions = $newDeductions->count() > 0;
            $hasAnyDeductions = $allActiveDeductions->count() > 0;
            
            return response()->json([
                'success' => true,
                'hasNewDeductions' => $hasNewDeductions,
                'hasAnyDeductions' => $hasAnyDeductions,
                'totalAmount' => $totalAmount,
                'count' => $newDeductions->count(),
                'deductions' => $newDeductions->map(function($deduction) {
                    return [
                        'id' => $deduction->id,
                        'amount' => $deduction->amount,
                        'description' => $deduction->description,
                        'giver_name' => $deduction->giver ? $deduction->giver->name : 'Unknown',
                        'created_at' => $deduction->created_at->format('Y-m-d H:i:s')
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking deductions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to check deductions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark deduction notifications as read
     */
    public function markNotificationRead()
    {
        try {
            // Just return success for now since we're using date-based logic
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error marking notifications as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to mark notifications as read'
            ], 500);
        }
    }
}
