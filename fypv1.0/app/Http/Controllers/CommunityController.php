<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Community::with(['user', 'comments.user']);

        // Get unique tags for the filter dropdown
        $tags = Community::distinct()->pluck('tag')->filter()->values();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $searchIn = $request->input('search_in', ['title']); // Default to title if not specified
            
            $query->where(function($q) use ($searchTerm, $searchIn) {
                if (in_array('title', $searchIn)) {
                    $q->orWhere('title', 'LIKE', "%{$searchTerm}%");
                }
                if (in_array('content', $searchIn)) {
                    $q->orWhere('content', 'LIKE', "%{$searchTerm}%");
                }
            });
        }

        // Date filter
        if ($request->has('date_filter') && !empty($request->date_filter)) {
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Post type filter
        if ($request->has('post_type') && !empty($request->post_type)) {
            $query->where('post_type', $request->post_type);
        }

        // Has image filter
        if ($request->has('has_image') && $request->has_image == '1') {
            $query->whereNotNull('cover_image');
        }

        // Saved posts filter
        if ($request->has('saved') && $request->saved === 'true') {
            $query->whereHas('savedByUsers', function($q) {
                $q->where('user_id', auth()->id());
            });
        }

        // Tag filter
        if ($request->has('tag') && !empty($request->tag)) {
            $query->where('tag', $request->tag);
        }

        $posts = $query->latest()->paginate(10)->withQueryString();
        
        return view('community', compact('posts', 'tags'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'tag' => 'required_without:new_tag|string',
                'new_tag' => 'nullable|required_if:tag,new|string|max:50',
                'post_type' => 'required|in:question,experience,discussion',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            
            \Log::info('Validated Data:', $validated);
    
            if ($request->hasFile('cover_image')) {
                $file = $request->file('cover_image');
                if ($file->isValid()) {
                    $path = $file->store('community-covers', 'public');
                    $validated['cover_image'] = $path;
                }
            }
    
            if ($request->tag === 'new' && $request->new_tag) {
                $validated['tag'] = $request->new_tag;
            }
    
            $validated['user_id'] = auth()->id();
    
            Community::create($validated);
    
            return redirect()->route('community.index')->with('success', 'Post created successfully!');
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Error:', $e->validator->errors()->toArray());
            return redirect()->back()
                ->withInput()
                ->withErrors($e->validator->errors());
        } catch (\Exception $e) {
            \Log::error('General Error:', ['message' => $e->getMessage()]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create post: ' . $e->getMessage()]);
        }
    }

    public function show(Community $community)
    {
        $community->load(['user', 'comments.user']);
        $isSaved = $community->isSavedBy(auth()->user());
        
        return view('community.show', compact('community', 'isSaved'));
    }

    public function toggleSave(Community $community)
    {
        $user = auth()->user();
        $saved = true;

        if ($community->isSavedBy($user)) {
            $community->savedByUsers()->detach($user->id);
            $saved = false;
        } else {
            $community->savedByUsers()->attach($user->id);
        }

        return response()->json([
            'success' => true,
            'saved' => $saved
        ]);
    }

    public function addComment(Request $request, Community $community)
    {
        $validated = $request->validate([
            'content' => 'required|max:1000'
        ]);

        $comment = $community->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content']
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user')
        ]);
    }

    public function getComments(Community $community)
    {
        $comments = $community->comments()
            ->with('user')
            ->latest()
            ->get();

        return response()->json([
            'comments' => $comments
        ]);
    }

    public function react(Request $request, Community $community)
    {
        $validated = $request->validate([
            'reaction_type' => 'required|in:like,dislike'
        ]);

        $user = auth()->user();
        $existingReaction = $community->reactions()
            ->where('user_id', $user->id)
            ->first();

        if ($existingReaction) {
            if ($existingReaction->reaction_type === $validated['reaction_type']) {
                // If same reaction type, remove it
                $existingReaction->delete();
            } else {
                // If different reaction type, update it
                $existingReaction->update(['reaction_type' => $validated['reaction_type']]);
            }
        } else {
            // Create new reaction
            $community->reactions()->create([
                'user_id' => $user->id,
                'reaction_type' => $validated['reaction_type']
            ]);
        }

        // Get fresh counts
        $likes = $community->reactions()->where('reaction_type', 'like')->count();
        $dislikes = $community->reactions()->where('reaction_type', 'dislike')->count();

        return response()->json([
            'success' => true,
            'likes' => $likes,
            'dislikes' => $dislikes
        ]);
    }

    public function destroy(Community $community)
    {
        if ($community->user_id !== Auth::id()) {
            abort(403);
        }

        $community->delete();

        return redirect()->route('community.index')
            ->with('success', 'Post deleted successfully!');
    }
}
