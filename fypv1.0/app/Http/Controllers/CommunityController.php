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

        // Apply sorting
        $sort = $request->sort ?? 'newest';
        switch ($sort) {
            case 'trending':
                // Most Popular based on likes and comments count
                $query->withCount([
                    'reactions as likes_count' => function($q) {
                        $q->where('reaction_type', 'like');
                    },
                    'comments as comments_count'
                ])
                ->selectRaw('
                    ((SELECT COUNT(*) FROM community_reactions 
                    WHERE community_reactions.community_id = communities.id 
                    AND reaction_type = "like") + 
                    (SELECT COUNT(*) FROM community_comments 
                    WHERE community_comments.community_id = communities.id)) as popularity_score
                ')
                ->orderByDesc('popularity_score');
                break;
            case 'higher_rate':
                // Posts with high likes, high saves, and low dislikes
                $query->withCount([
                    'reactions as likes_count' => function($q) {
                        $q->where('reaction_type', 'like');
                    },
                    'reactions as dislikes_count' => function($q) {
                        $q->where('reaction_type', 'dislike');
                    },
                    'savedByUsers as saves_count'
                ])
                ->selectRaw('
                    (SELECT COUNT(*) FROM community_reactions 
                    WHERE community_reactions.community_id = communities.id 
                    AND reaction_type = "like") + 
                    (SELECT COUNT(*) FROM community_saved_posts 
                    WHERE community_saved_posts.community_id = communities.id) - 
                    ((SELECT COUNT(*) FROM community_reactions 
                    WHERE community_reactions.community_id = communities.id 
                    AND reaction_type = "dislike") * 2) as rating_score
                ')
                ->orderByDesc('rating_score');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $posts = $query->paginate(10)->withQueryString();
        
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
        try {
            // Add logging to debug the incoming request
            \Log::info('Comment Request:', [
                'content' => $request->input('content'),
                'all_data' => $request->all()
            ]);

            $validated = $request->validate([
                'content' => 'required|max:1000'
            ]);

            $comment = $community->comments()->create([
                'user_id' => auth()->id(),
                'content' => $validated['content']
            ]);

            $comment->load('user'); // Make sure to load the user relationship

            // Add logging to debug the created comment
            \Log::info('Created Comment:', [
                'comment' => $comment->toArray()
            ]);

            return response()->json([
                'success' => true,
                'comment' => $comment
            ]);
        } catch (\Exception $e) {
            \Log::error('Comment creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create comment'
            ], 500);
        }
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

    public function getMyPosts()
    {
        try {
            $posts = Community::where('user_id', auth()->id())
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'posts' => $posts
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getMyPosts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts'
            ], 500);
        }
    }

    public function updatePost(Request $request, Community $community)
    {
        // Check if user owns the post
        if ($community->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'post_type' => 'required|in:question,experience,discussion',
                'tag' => 'required|string|max:50',
            ]);

            $community->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully',
                'post' => $community
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update post'
            ], 500);
        }
    }

    public function destroy(Community $community)
    {
        if ($community->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            // Delete the cover image if it exists
            if ($community->cover_image && Storage::disk('public')->exists($community->cover_image)) {
                Storage::disk('public')->delete($community->cover_image);
            }

            $community->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post'
            ], 500);
        }
    }
}
