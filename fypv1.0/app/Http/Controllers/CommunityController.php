<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommunityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $posts = Community::with('user')
            ->latest()
            ->paginate(10);

        return view('community', compact('posts'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'tag' => 'required_without:new_tag|string',
                'new_tag' => 'required_if:tag,new|string|max:50',
                'post_type' => 'required|in:question,experience,discussion',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('cover_image')) {
                $file = $request->file('cover_image');
                if ($file->isValid()) {
                    $path = $file->store('community-covers', 'public');
                    if (!$path) {
                        throw new \Exception('Failed to store image');
                    }
                    $validated['cover_image'] = $path;
                }
            }

            if ($request->tag === 'new' && $request->new_tag) {
                $validated['tag'] = $request->new_tag;
            } else {
                $validated['tag'] = $request->tag;
            }

            $validated['user_id'] = auth()->id();
            
            Community::create($validated);

            return redirect()->route('community.index')
                ->with('success', 'Post created successfully!');

        } catch (\Exception $e) {
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create post: ' . $e->getMessage()]);
        }
    }
} 