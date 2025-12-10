<?php

namespace App\Http\Controllers;

use App\Models\Dont;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DontsController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'dontWhat' => 'required|string|max:100',
                'dontWhy' => 'required|string',
                'dontImpact' => 'required|string',
                'dontSeverity' => 'required|in:Critical,High,Medium'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $dont = Dont::create([
                'user_id' => Auth::id(),
                'what' => $request->dontWhat,
                'why' => $request->dontWhy,
                'impact' => $request->dontImpact,
                'severity' => $request->dontSeverity
            ]);

            return response()->json([
                'success' => true,
                'message' => 'DON\'T saved successfully!',
                'data' => $dont
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving DON\'T: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            $donts = Dont::byUser(Auth::id())
                        ->active()
                        ->orderBy('severity', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->get();

            return response()->json([
                'success' => true,
                'data' => $donts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching DON\'Ts: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $dont = Dont::byUser(Auth::id())->findOrFail($id);
            $dont->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'DON\'T deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting DON\'T: ' . $e->getMessage()
            ], 500);
        }
    }
}
