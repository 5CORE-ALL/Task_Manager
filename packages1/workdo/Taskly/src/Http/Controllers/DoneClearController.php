<?php

namespace Workdo\Taskly\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Taskly\Entities\DoneClear;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DoneClearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Special email addresses that can see all data
        $specialEmails = [
            'president@5core.com',
            'hr@5core.com',
            'tech-support@5core.com',
            'support@5core.com',
            'mgr-advertisement@5core.com',
            'mgr-content@5core.com',
            'ritu.kaur013@gmail.com',
            'sjoy7486@gmail.com',
            'ecomm2@5core.com',
            'sr.manager@5core.com',
            'inventory@5core.com',
        ];

        $currentUserEmail = Auth::user()->email;
        $currentUserId = Auth::user()->id;

        // If user has special email, show all data
        if (in_array($currentUserEmail, $specialEmails)) {
            $doneClears = DoneClear::with(['assignor', 'assignee', 'creator'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Regular users only see their own data (as assignee or assignor)
            $doneClears = DoneClear::with(['assignor', 'assignee', 'creator'])
                ->where(function($query) use ($currentUserId) {
                    $query->where('assignor_id', $currentUserId)
                          ->orWhere('assignee_id', $currentUserId);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Get all users for dropdown (same logic as header.blade.php)
        $users = User::select('id', 'name', 'email')->get();

        return view('taskly::done-clear.index', compact('doneClears', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Add logging for debugging
        \Log::info('DoneClear store request received', [
            'user_id' => Auth::user()->id,
            'request_data' => $request->all(),
            'timestamp' => now(),
        ]);
        
        $validator = Validator::make($request->all(), [
            'assignor_id' => 'required|exists:users,id',
            'assignee_id' => 'required|exists:users,id',
            'description' => 'required|string|max:1000',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get user names
            $assignor = User::find($request->assignor_id);
            $assignee = User::find($request->assignee_id);

            $doneClear = DoneClear::create([
                'assignor_id' => $request->assignor_id,
                'assignor_name' => $assignor->name,
                'assignee_id' => $request->assignee_id,
                'assignee_name' => $assignee->name,
                'description' => $request->description,
                'priority' => $request->priority,
                'created_by' => Auth::user()->id,
                'workspace' => getActiveWorkSpace(),
            ]);

            \Log::info('DoneClear created successfully', [
                'id' => $doneClear->id,
                'assignor' => $assignor->name,
                'assignee' => $assignee->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Done Clear task created successfully!',
                'data' => $doneClear->load(['assignor', 'assignee', 'creator'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the task.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users for AJAX dropdown
     */
    public function getUsers()
    {
        try {
            $users = User::select('id', 'name', 'email')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $doneClear = DoneClear::findOrFail($id);
            
            // Check if user can delete (only creator or special emails)
            $specialEmails = [
                'president@5core.com',
                'hr@5core.com',
                'tech-support@5core.com',
                'support@5core.com',
                'mgr-advertisement@5core.com',
                'mgr-content@5core.com',
                'ritu.kaur013@gmail.com',
                'sjoy7486@gmail.com',
                'ecomm2@5core.com',
                'sr.manager@5core.com',
                'inventory@5core.com',
            ];

            $currentUserEmail = Auth::user()->email;
            $currentUserId = Auth::user()->id;

            if ($doneClear->created_by !== $currentUserId && !in_array($currentUserEmail, $specialEmails)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this task.'
                ], 403);
            }

            $doneClear->delete();

            return response()->json([
                'success' => true,
                'message' => 'Done Clear task deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the task.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
