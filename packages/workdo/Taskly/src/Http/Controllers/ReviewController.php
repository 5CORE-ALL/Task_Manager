<?php

namespace Workdo\Taskly\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Taskly\DataTables\ReviewDataTable;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ReviewDataTable $dataTable)
    {
        // Check if user is admin or president@5core.com
        if (Auth::user()->type != 'super admin' && Auth::user()->email != 'president@5core.com') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        return $dataTable->render('taskly::reviews.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Check if user is admin or president@5core.com
        if (Auth::user()->type != 'super admin' && Auth::user()->email != 'president@5core.com') {
            return response()->json(['error' => __('Permission denied.')], 403);
        }

        try {
            $review = Review::find($id);
            
            if (!$review) {
                return response()->json(['error' => __('Review not found.')], 404);
            }

            $review->delete();
            
            return response()->json(['success' => __('Review deleted successfully.')]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('Something went wrong.')], 500);
        }
    }
}
