<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyShippingChecklist;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DailyShippingChecklistController extends Controller
{
    public function index()
    {
        $checklists = DailyShippingChecklist::orderBy('checklist_date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        return view('daily-shipping-checklist.index', compact('checklists'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'checklist_date' => 'required|date',
            'task_1' => 'required|in:Yes,No',
            'task_2' => 'required|in:Yes,No',
            'task_3' => 'required|in:Yes,No',
            'task_4' => 'required|in:Yes,No',
            'task_1_comments' => 'nullable|string',
            'task_2_comments' => 'nullable|string',
            'task_3_comments' => 'nullable|string',
            'task_4_comments' => 'nullable|string',
        ]);
        
        // Check if user already submitted checklist for today
        $existingChecklist = DailyShippingChecklist::where('user_id', Auth::id())
                            ->where('checklist_date', $request->checklist_date)
                            ->first();
        
        if ($existingChecklist) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted a checklist for today.'
            ], 400);
        }
        
        $checklist = DailyShippingChecklist::create([
            'user_name' => $request->user_name,
            'checklist_date' => $request->checklist_date,
            'task_1' => $request->task_1,
            'task_1_comments' => $request->task_1_comments,
            'task_2' => $request->task_2,
            'task_2_comments' => $request->task_2_comments,
            'task_3' => $request->task_3,
            'task_3_comments' => $request->task_3_comments,
            'task_4' => $request->task_4,
            'task_4_comments' => $request->task_4_comments,
            'user_id' => Auth::id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Daily Shipping Checklist saved successfully!',
            'checklist' => $checklist
        ]);
    }
    
    public function destroy($id)
    {
        $checklist = DailyShippingChecklist::findOrFail($id);
        $checklist->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Checklist deleted successfully!'
        ]);
    }
}
