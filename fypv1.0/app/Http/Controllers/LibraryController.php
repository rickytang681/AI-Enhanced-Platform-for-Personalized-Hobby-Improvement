<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use App\Models\LibraryReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = LibraryItem::query();

        // Add saved filter
        if ($request->saved === 'true') {
            $query->whereHas('saves', function($q) {
                $q->where('user_id', auth()->id());
            });
        }

        // Full-text search with specified fields
        if ($request->search) {
            $searchTerm = $request->search;
            $searchIn = $request->search_in ?? ['title', 'description']; // Default search fields

            $query->where(function($q) use ($searchTerm, $searchIn) {
                foreach ($searchIn as $field) {
                    $q->orWhere($field, 'like', '%' . $searchTerm . '%');
                }
            });
        }

        // Date filter
        if ($request->date_filter) {
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

        // Resource type filter
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Category filter
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Subcategory/Level filter
        if ($request->subcategory) {
            $query->where('subcategory', $request->subcategory);
        }

        // Apply sorting
        $sort = $request->sort ?? 'newest';
        switch ($sort) {
            case 'popular':
                $query->orderBy('likes', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'rated':
                $query->orderByRaw('(likes - dislikes) DESC');
                break;
        }

        $items = $query->with('user')->paginate(10)->withQueryString();

        return view('library', [
            'items' => $items,
            'categories' => $this->getCategories(),
            'subcategories' => $this->getSubcategories(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'category' => 'required_without:new_category|string|max:50',
                'new_category' => 'required_if:category,new|string|max:50|nullable',
                'subcategory' => 'required|string|max:50',
                'type' => 'required|in:video,text',
                'content' => 'required_if:type,text|nullable|string',
                'video_url' => 'required_if:type,video|nullable|url',
                'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,txt,mp4,zip,rar',
            ]);

            // Handle new category
            if ($request->category === 'new' && $request->new_category) {
                $validated['category'] = $request->new_category;
                
                // Add new category to the list
                $categories = $this->getCategories();
                if (!in_array($validated['category'], $categories)) {
                    $categories[] = $validated['category'];
                    sort($categories);
                    cache()->forever('library_categories', $categories);
                }
            }

            // Handle file upload if present
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('library-files', $filename, 'public');
                $validated['file_path'] = $path;
            }

            // Set default values
            $validated['user_id'] = auth()->id();
            $validated['likes'] = 0;
            $validated['dislikes'] = 0;
            $validated['average_rating'] = 0;
            $validated['rating_count'] = 0;

            // Remove null values and empty strings
            $validated = array_filter($validated, function($value) {
                return !is_null($value) && $value !== '';
            });

            $item = LibraryItem::create($validated);

            return redirect()->route('library')
                ->with('success', 'Resource uploaded successfully!');

        } catch (\Exception $e) {
            \Log::error('Library store error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'user' => auth()->id()
            ]);

            // Clean up uploaded file if exists
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create resource. Please check your input and try again.']);
        }
    }

    public function react(Request $request, LibraryItem $item)
    {
        $validated = $request->validate([
            'reaction_type' => 'required|in:like,dislike',
        ]);

        $existing = LibraryReaction::where('user_id', auth()->id())
            ->where('library_item_id', $item->id)
            ->first();

        if ($existing) {
            if ($existing->reaction_type === $validated['reaction_type']) {
                $existing->delete();
                $this->updateReactionCount($item);
                return response()->json([
                    'message' => 'Reaction removed',
                    'likes' => $item->likes,
                    'dislikes' => $item->dislikes
                ]);
            }
            $existing->update($validated);
        } else {
            LibraryReaction::create([
                'user_id' => auth()->id(),
                'library_item_id' => $item->id,
                'reaction_type' => $validated['reaction_type'],
            ]);
        }

        $this->updateReactionCount($item);
        return response()->json([
            'message' => 'Reaction updated',
            'likes' => $item->likes,
            'dislikes' => $item->dislikes
        ]);
    }

    private function updateReactionCount(LibraryItem $item)
    {
        $item->update([
            'likes' => $item->reactions()->where('reaction_type', 'like')->count(),
            'dislikes' => $item->reactions()->where('reaction_type', 'dislike')->count(),
        ]);
    }

    private function getCategories()
    {
        // Get categories from cache or default list
        return cache()->remember('library_categories', now()->addWeek(), function() {
            return [
                'Photography', 'Coding', 'Reading', 'Video Games',
                'Writing', 'Music', 'Sports', 'Cooking',
                'Gardening', 'Arts', 'Crafts', 'Running'
            ];
        });
    }

    private function getSubcategories()
    {
        return [
            'Beginner',
            'Intermediate',
            'Advanced'
        ];
    }

    public function addComment(Request $request, LibraryItem $item)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment = $item->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content']
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user')
        ]);
    }

    public function rate(Request $request, LibraryItem $item)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5'
        ]);

        $rating = $item->ratings()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['rating' => $validated['rating']]
        );

        // Update average rating
        $item->update([
            'average_rating' => $item->ratings()->avg('rating'),
            'rating_count' => $item->ratings()->count()
        ]);

        return response()->json([
            'success' => true,
            'rating' => $validated['rating'],
            'average' => $item->average_rating,
            'count' => $item->rating_count
        ]);
    }

    public function toggleSave(LibraryItem $item)
    {
        $save = $item->saves()->where('user_id', auth()->id())->first();

        if ($save) {
            $save->delete();
            $saved = false;
        } else {
            $item->saves()->create(['user_id' => auth()->id()]);
            $saved = true;
        }

        return response()->json([
            'success' => true,
            'saved' => $saved
        ]);
    }

    public function getComments(LibraryItem $item)
    {
        $comments = $item->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'comments' => $comments
        ]);
    }
} 