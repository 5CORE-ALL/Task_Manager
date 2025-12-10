<?php

namespace App\Http\Controllers;

use App\Models\Incentive;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IncentiveController extends Controller
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
            Auth::user()->email == 'mgr-content@5core.com') {
            
            // Get ALL incentives for admin
            $incentives = Incentive::with(['giver', 'receiver'])
                                 ->orderBy('created_at', 'desc')
                                 ->get();
            
            // Pass to admin view
            return view('incentives.admin_index', compact('incentives'));
        }
        
        // Regular employees see only incentives they have received
        $incentives = Incentive::with(['giver', 'receiver'])
                              ->where('receiver_id', Auth::id())
                              ->orderBy('created_at', 'desc')
                              ->get();
        
        return view('incentives.index', compact('incentives'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user has permission to give incentives
        if (!in_array(Auth::user()->email, ['president@5core.com', 'tech-support@5core.com'])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to give incentives.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'incentive_team_member_id' => 'required|exists:users,id',
            'incentive_amount' => 'required|numeric|min:0.01',
            // 'start_date' => 'required|date',
            'end_date' => 'required|date',
            'incentive_description' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get the receiver user
            $receiver = User::find($request->input('incentive_team_member_id'));
            if (!$receiver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected team member not found.'
                ], 404);
            }

            // Prepare incentive data
            $incentiveData = [
                'giver_id' => Auth::id(),
                'receiver_id' => $request->input('incentive_team_member_id'),
                'employee_id' => $request->input('incentive_team_member_id'),
                'amount' => $request->input('incentive_amount'),
                // 'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'description' => $request->input('incentive_description'),
                'status' => 'active',
                'workspace_id' => 1,
                'created_by' => Auth::id(),
                // Add fields for backward compatibility
                'employee_name' => $receiver->name,
                'department' => $receiver->department ?? 'General',
                'incentive_month' => date('Y-m'),
                'requested_incentive' => $request->input('incentive_amount'),
                'incentive_reason' => $request->input('incentive_description'),
                'approved_incentive' => $request->input('incentive_amount'),
                'approval_reason' => 'Auto-approved by ' . Auth::user()->name,
                'approved_by' => Auth::id(),
                'review_date' => now(),
                'workspace' => 'default'
            ];

            $incentive = Incentive::create($incentiveData);

            return response()->json([
                'success' => true,
                'message' => 'Incentive submitted successfully!',
                'data' => $incentive
            ]);

        } catch (\Exception $e) {
            \Log::error('Incentive creation failed: ' . $e->getMessage());
            
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
        $incentive = Incentive::with(['giver', 'receiver'])->find($id);
        
        if (!$incentive) {
            return response()->json(['error' => 'Incentive not found.'], 404);
        }

        // Check if user can view this incentive
        if (Auth::user()->type != 'super admin' && 
            !in_array(Auth::user()->email, ['president@5core.com', 'tech-support@5core.com']) &&
            $incentive->receiver_id != Auth::id()) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }
        
        return view('incentives.show', compact('incentive'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Check if user has permission to delete incentives
        if (!in_array(Auth::user()->email, ['president@5core.com', 'tech-support@5core.com']) &&
            Auth::user()->type != 'super admin') {
            return response()->json(['error' => __('Permission denied.')], 403);
        }

        try {
            $incentive = Incentive::find($id);
            
            if (!$incentive) {
                return response()->json(['error' => __('Incentive not found.')], 404);
            }

            $incentive->delete();
            
            return response()->json(['success' => __('Incentive deleted successfully.')]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('Something went wrong.')], 500);
        }
    }

    /**
     * Check for incentives via AJAX
     */
    public function checkIncentives()
    {
        try {
            $user = Auth::user();
            
            // Get ALL active incentives for the current user (for header display)
            $allActiveIncentives = Incentive::where('receiver_id', $user->id)
                                          ->where('status', 'active')
                                          ->get();
            
            // Get incentives created in last 24 hours for popup
            $newIncentives = Incentive::where('receiver_id', $user->id)
                                    ->where('status', 'active')
                                    ->where('created_at', '>', now()->subDay())
                                    ->get();
            
            $totalAmount = $allActiveIncentives->sum('amount');
            $hasNewIncentives = $newIncentives->count() > 0;
            $hasAnyIncentives = $allActiveIncentives->count() > 0;
            
            return response()->json([
                'success' => true,
                'hasNewIncentives' => $hasNewIncentives,
                'hasAnyIncentives' => $hasAnyIncentives,
                'totalAmount' => $totalAmount,
                'count' => $newIncentives->count(),
                'incentives' => $newIncentives->map(function($incentive) {
                    return [
                        'id' => $incentive->id,
                        'amount' => $incentive->amount,
                        'description' => $incentive->description,
                        'giver_name' => $incentive->giver ? $incentive->giver->name : 'Unknown',
                        'created_at' => $incentive->created_at->format('Y-m-d H:i:s')
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking incentives: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to check incentives: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark incentive notifications as read
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
