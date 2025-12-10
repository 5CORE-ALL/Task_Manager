<?php

namespace App\Http\Controllers;

use App\Models\TaskActivityReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskActivityReportController extends Controller
{
    public function index(Request $request)
    {
        // Check if user is authorized
        $authorizedEmails = ['president@5core.com', 'tech-support@5core.com', 'inventory@5core.com'];
        $userEmail = Auth::user()->email;
        
        if (!in_array($userEmail, $authorizedEmails)) {
            abort(403, 'You are not authorized to view this page.');
        }

        $query = TaskActivityReport::notDeleted()->latest('activity_date');

        // Apply filters if provided
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('activity_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('activity_date', '<=', $request->date_to);
        }

        if ($request->filled('task_name')) {
            $query->where('task_name', 'like', '%' . $request->task_name . '%');
        }

        $activities = $query->paginate(15);

        return view('task-activity-report.index', compact('activities'));
    }

    public function restore($id)
    {
        $authorizedEmails = ['president@5core.com', 'tech-support@5core.com', 'inventory@5core.com'];
        $userEmail = Auth::user()->email;
        
        if (!in_array($userEmail, $authorizedEmails)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $activity = TaskActivityReport::findOrFail($id);
        
        if ($activity->activity_type === 'delete') {
            // Log restore activity
            TaskActivityReport::logActivity(
                $activity->task_name,
                'restore',
                Auth::user()->name,
                Auth::user()->email,
                request()->ip(),
                'Task restored from deletion'
            );
            
            return response()->json(['success' => 'Task restore logged successfully']);
        }

        return response()->json(['error' => 'Only deleted tasks can be restored'], 400);
    }

    public function destroy($id)
    {
        $authorizedEmails = ['president@5core.com', 'tech-support@5core.com', 'inventory@5core.com'];
        $userEmail = Auth::user()->email;
        
        if (!in_array($userEmail, $authorizedEmails)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $activity = TaskActivityReport::findOrFail($id);
        $activity->update(['is_deleted' => true]);

        return response()->json(['success' => 'Activity record deleted successfully']);
    }
}
