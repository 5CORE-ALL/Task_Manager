<?php

namespace App\Http\Controllers;

use App\Models\Dos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DosController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'dosWhat' => 'required|string|max:100',
                'dosWhy' => 'required|string',
                'dosImpact' => 'required|string',
                'dosPriority' => 'required|in:High,Medium,Low'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $dos = Dos::create([
                'user_id' => Auth::id(),
                'what' => $request->dosWhat,
                'why' => $request->dosWhy,
                'impact' => $request->dosImpact,
                'priority' => $request->dosPriority
            ]);

            return response()->json([
                'success' => true,
                'message' => 'DO saved successfully!',
                'data' => $dos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving DO: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            $dos = Dos::byUser(Auth::id())
                     ->active()
                     ->orderBy('priority', 'desc')
                     ->orderBy('created_at', 'desc')
                     ->get();

            return response()->json([
                'success' => true,
                'data' => $dos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching DOs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $dos = Dos::byUser(Auth::id())->findOrFail($id);
            $dos->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'DO deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting DO: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show DO's and DON'T Report Page
     */
    public function report()
    {
        try {
            $userId = Auth::id();
            
            // Get DO's data
            $dos = \App\Models\Dos::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Get DON'T data
            $donts = \App\Models\Dont::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return view('dos-donts.report', compact('dos', 'donts'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading report: ' . $e->getMessage());
        }
    }
}
