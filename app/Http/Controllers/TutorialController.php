<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tutorial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class TutorialController extends Controller
{
    public function index()
    {
        // $task_manager
        return view('tutorial');
    } 

 public function store(Request $request)
    {
        $request->validate([
            'video_link' => 'required|url',
            'thumbnail_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'thumbnail_name' => 'required|string|max:255',
            'menu_type' => 'required|string|max:100',
        ]);

        // $path = $request->file('thumbnail_image')->store('tutorial_thumbnails', 'public');
        $path = null;
if ($request->hasFile('thumbnail_image')) {
    $path = $request->file('thumbnail_image')->store('tutorial_thumbnails', 'public');
}

        Tutorial::create([
            'user_id' => Auth::id() ?? 1,
            'video_link' => $request->video_link,
            'thumbnail_image' => $path,
            'thumbnail_name' => $request->thumbnail_name,
            'menu_type' => $request->menu_type,
        ]);

        return response()->json(['message' => 'Tutorial added successfully!']);
    }

    public function getByCategory(Request $request)
    {
        if ($request->has('tutorial_id')) {
            $video = Tutorial::where('id', $request->tutorial_id)->get();
            return response()->json(['videos' => $video]);
        }

        $videos = Tutorial::where('menu_type', $request->menu_type)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json(['videos' => $videos]);
    }

    public function update(Request $request, $id)
    {
        $tutorial = Tutorial::findOrFail($id);

        $request->validate([
            'video_link' => 'required|url',
            'thumbnail_name' => 'required|string|max:255',
            'menu_type' => 'required|string|max:100',
            'thumbnail_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('thumbnail_image')) {
            if ($tutorial->thumbnail_image && Storage::disk('public')->exists($tutorial->thumbnail_image)) {
                Storage::disk('public')->delete($tutorial->thumbnail_image);
            }

            $path = $request->file('thumbnail_image')->store('tutorial_thumbnails', 'public');
            $tutorial->thumbnail_image = $path;
        }

        $tutorial->video_link = $request->video_link;
        $tutorial->thumbnail_name = $request->thumbnail_name;
        $tutorial->menu_type = $request->menu_type;
        $tutorial->save();

        return response()->json(['message' => 'Tutorial updated successfully!']);
    }

    public function destroy($id)
    {
        $tutorial = Tutorial::findOrFail($id);

        if ($tutorial->thumbnail_image && Storage::disk('public')->exists($tutorial->thumbnail_image)) {
            Storage::disk('public')->delete($tutorial->thumbnail_image);
        }

        $tutorial->delete();

        return response()->json(['message' => 'Tutorial deleted successfully!']);
    }

}
