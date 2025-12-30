<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class BlogController extends Controller
{
    public function index()
    {
        return view('blog');
    } 

    public function store(Request $request)
    {
        $request->validate([
            'blog_url' => 'required|url',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'title' => 'required|string|max:255',
            'menu_type' => 'required|string|max:100',
        ]);

        $path = null;
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('blog_images', 'public');
        }

        Blog::create([
            'user_id' => Auth::id() ?? 1,
            'blog_url' => $request->blog_url,
            'featured_image' => $path,
            'title' => $request->title,
            'menu_type' => $request->menu_type,
        ]);

        return response()->json(['message' => 'Blog added successfully!']);
    }

    public function getByCategory(Request $request)
    {
        if ($request->has('blog_id')) {
            $blog = Blog::where('id', $request->blog_id)->get();
            return response()->json(['blogs' => $blog]);
        }

        $blogs = Blog::where('menu_type', $request->menu_type)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json(['blogs' => $blogs]);
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'blog_url' => 'required|url',
            'title' => 'required|string|max:255',
            'menu_type' => 'required|string|max:100',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('featured_image')) {
            if ($blog->featured_image && Storage::disk('public')->exists($blog->featured_image)) {
                Storage::disk('public')->delete($blog->featured_image);
            }

            $path = $request->file('featured_image')->store('blog_images', 'public');
            $blog->featured_image = $path;
        }

        $blog->blog_url = $request->blog_url;
        $blog->title = $request->title;
        $blog->menu_type = $request->menu_type;
        $blog->save();

        return response()->json(['message' => 'Blog updated successfully!']);
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);

        if ($blog->featured_image && Storage::disk('public')->exists($blog->featured_image)) {
            Storage::disk('public')->delete($blog->featured_image);
        }

        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully!']);
    }
}

