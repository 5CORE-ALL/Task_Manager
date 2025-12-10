<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class MomController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        $currentUserEmail = strtolower($currentUser->email);
        
        // Get MOMs where current user is assignee or host
        $moms = DB::table('moms')
            ->where(function($query) use ($currentUserEmail) {
                $query->where('host_email', $currentUserEmail)
                      ->orWhere('assignees', 'LIKE', '%' . $currentUserEmail . '%');
            })
            ->orderBy('meeting_date', 'desc')
            ->get();

        // Get all employees for filters
        $employees = User::select('name', 'email')->get();

        return view('mom.index', compact('moms', 'employees', 'currentUser'));
    }

    public function create()
    {
        $employees = User::select('name', 'email')->get();
        return view('mom.create', compact('employees'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'meeting_name' => 'required|string|max:255',
                'meeting_date' => 'required|date',
                'location' => 'required|string|max:255',
                'host_name' => 'required|string|max:255',
                'assignees' => 'nullable|array',
                'agenda' => 'required|string'
            ]);

            $currentUser = Auth::user();
            
            // Convert assignees array to comma-separated string
            $assigneesString = '';
            if ($request->assignees && is_array($request->assignees)) {
                $assigneesString = implode(',', $request->assignees);
            }
            
            DB::table('moms')->insert([
                'meeting_name' => $request->meeting_name,
                'meeting_date' => $request->meeting_date,
                'location' => $request->location,
                'host_name' => $request->host_name,
                'host_email' => strtolower($currentUser->email),
                'assignees' => $assigneesString,
                'agenda' => $request->agenda,
                'created_by' => $currentUser->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'MOM created successfully!'
                ]);
            }

            return redirect()->route('mom.index')->with('success', 'MOM created successfully!');
            
        } catch (\Exception $e) {
            \Log::error('MOM Creation Error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating MOM: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error creating MOM: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $mom = DB::table('moms')->where('id', $id)->first();
        
        if (!$mom) {
            return response()->json(['error' => 'MOM not found'], 404);
        }

        return response()->json($mom);
    }

    public function destroy($id)
    {
        $currentUser = Auth::user();
        $currentUserEmail = strtolower($currentUser->email);
        
        $mom = DB::table('moms')->where('id', $id)->first();
        
        if (!$mom) {
            return response()->json(['error' => 'MOM not found'], 404);
        }

        // Check if current user is host or has permission to delete
        if ($mom->host_email !== $currentUserEmail) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::table('moms')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'MOM deleted successfully!'
        ]);
    }
}
