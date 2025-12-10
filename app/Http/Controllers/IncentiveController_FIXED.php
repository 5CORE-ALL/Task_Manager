<?php

namespace App\Http\Controllers;

use App\Models\Incentive;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class IncentiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if user is admin or special email for DataTable view
        if (Auth::user()->email == 'president@5core.com' || 
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
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
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

            // Prepare incentive data - include ALL possible fields to avoid constraint errors
            $incentiveData = [
                'giver_id' => Auth::id(),
                'receiver_id' => $request->input('incentive_team_member_id'),
                'amount' => $request->input('incentive_amount'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'description' => $request->input('incentive_description'),
                'status' => 'active',
                'workspace_id' => 1,
                'created_by' => Auth::id(),
                // Add ALL fields for backward compatibility
                'employee_id' => $request->input('incentive_team_member_id'), // Use user ID as employee ID
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

            Log::info('Creating incentive with data:', $incentiveData);

            $incentive = Incentive::create($incentiveData);

            return response()->json([
                'success' => true,
                'message' => 'Incentive submitted successfully!',
                'data' => $incentive
            ]);

        } catch (\Exception $e) {
            Log::error('Incentive creation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
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
        if (!in_array(Auth::user()->email, ['president@5core.com', 'tech-support@5core.com']) &&
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
        if (!in_array(Auth::user()->email, ['president@5core.com', 'tech-support@5core.com'])) {
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
}
