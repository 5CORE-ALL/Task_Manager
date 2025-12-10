<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use App\DataTables\ReviewDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
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
            Auth::user()->email == 'sr.manager@5core.com' ||
            Auth::user()->email == 'ritu.kaur013@gmail.com') {
            
            // Get ALL reviews for admin - same as regular employees but ALL data
            $reviews = Review::with(['reviewer', 'reviewee'])
                            ->orderBy('created_at', 'desc')
                            ->get();
            
            // Pass to admin view with data
            return view('reviews.admin_index', compact('reviews'));
        }
        
        // Regular employees see only reviews they have received (not given)
        $reviews = Review::with(['reviewer', 'reviewee'])
                        ->where('reviewee_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        return view('reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = User::where('id', '!=', Auth::id())
                         ->select('id', 'name')
                         ->get();
        
        return view('reviews.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reviewee_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'description' => 'required|string|max:1000',
            'screenshot' => 'nullable|image|mimes:png,jpg,jpeg|max:5120' // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $screenshotPath = null;
            
            // Handle screenshot upload
            if ($request->hasFile('screenshot')) {
                $screenshot = $request->file('screenshot');
                $filename = 'review_' . time() . '_' . uniqid() . '.' . $screenshot->getClientOriginalExtension();
                
                // Create uploads/reviews directory if it doesn't exist
                $uploadPath = public_path('uploads/reviews');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                // Move the file
                $screenshot->move($uploadPath, $filename);
                $screenshotPath = 'uploads/reviews/' . $filename;
            }

            $review = Review::create([
                'reviewer_id' => Auth::id(),
                'reviewee_id' => $request->reviewee_id,
                'rating' => $request->rating,
                'description' => $request->description,
                'screenshot' => $screenshotPath,
                'workspace_id' => 1, // Set default workspace
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        $review->load(['reviewer', 'reviewee']);
        return view('reviews.show', compact('review'));
    }

    /**
     * Get employees for dropdown
     */
    public function getEmployees()
    {
        $employees = User::where('id', '!=', Auth::id())
                         ->select('id', 'name')
                         ->get();
        
        return response()->json($employees);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Check if user is admin or special emails
        if (Auth::user()->type != 'super admin' && 
            Auth::user()->email != 'president@5core.com' && 
            Auth::user()->email != 'tech-support@5core.com') {
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

    /**
     * Create sample review data for testing
     */
    public function createSampleData()
    {
        // Allow any logged-in user to create sample data for testing
        if (!Auth::check()) {
            return response()->json(['error' => 'Please login first'], 403);
        }

        // Get some users for sample data
        $users = User::limit(5)->get();
        
        if ($users->count() < 2) {
            return response()->json(['error' => 'Need at least 2 users to create sample reviews'], 400);
        }

        // Create sample reviews
        $sampleReviews = [
            [
                'reviewer_id' => $users[0]->id,
                'reviewee_id' => $users[1]->id,
                'rating' => 4,
                'description' => 'Great team player, always delivers on time. Excellent communication skills.',
                'workspace_id' => 1,
                'created_by' => Auth::id()
            ],
            [
                'reviewer_id' => $users[1]->id,
                'reviewee_id' => $users[0]->id,
                'rating' => 5,
                'description' => 'Outstanding performance this quarter. Shows great leadership qualities.',
                'workspace_id' => 1,
                'created_by' => Auth::id()
            ],
            [
                'reviewer_id' => $users[0]->id,
                'reviewee_id' => $users[2] ?? $users[1]->id,
                'rating' => 3,
                'description' => 'Good performance but needs improvement in communication.',
                'workspace_id' => 1,
                'created_by' => Auth::id()
            ]
        ];

        foreach ($sampleReviews as $reviewData) {
            // Check if this combination already exists
            $existing = Review::where('reviewer_id', $reviewData['reviewer_id'])
                            ->where('reviewee_id', $reviewData['reviewee_id'])
                            ->first();
            
            if (!$existing) {
                Review::create($reviewData);
            }
        }

        return response()->json([
            'success' => 'Sample reviews created successfully!',
            'count' => Review::count(),
            'message' => 'Now refresh the reviews page to see the data.'
        ]);
    }

    /**
     * Get data for DataTables AJAX
     */
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $dataTable = new ReviewDataTable();
            return $dataTable->dataTable($dataTable->query(new Review()))->make(true);
        }
        
        return response()->json(['error' => 'Invalid request'], 400);
    }
}
