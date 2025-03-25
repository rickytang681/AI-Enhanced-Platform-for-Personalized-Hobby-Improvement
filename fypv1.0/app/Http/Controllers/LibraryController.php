<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use App\Models\LibraryReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class LibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = LibraryItem::query();
        
        // Get categories and subcategories
        $categories = $this->getCategories();
        $subcategories = $this->getSubcategories();

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
                // Most Popular based on likes count
                $query->orderBy('likes', 'desc');
                break;
            case 'rated':
                // Highly Rated based on average rating
                $query->orderBy('average_rating', 'desc')
                      ->having('rating_count', '>', 0); // Only show items with ratings
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
        }

        $items = $query->paginate(10);
        
        // Pass all required variables to the view
        return view('library', compact('items', 'categories', 'subcategories'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'category' => 'required_without:new_category|string|max:50',
                'new_category' => 'required_without:category|nullable|string|max:50',
                'subcategory' => 'required|string|max:50',
                'type' => 'required|in:text,video',
                'content' => 'required_if:type,text|nullable|string',
                'video_url' => 'required_if:type,video|nullable|url',
                'file' => [
                    'nullable',
                    'file',
                    'max:10240',
                    'mimes:pdf,doc,docx,txt,mp4,zip,rar',
                ],
            ]);

            // Handle new category
            if ($request->category === 'new') {
                if (empty($request->new_category)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['new_category' => 'Please enter a new category name']);
                }
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
                
                // Additional file validation
                if (!$file->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['file' => 'The uploaded file is invalid or corrupted.']);
                }

                // Create storage directory if it doesn't exist
                $storage_path = 'public/library-files';
                if (!Storage::exists($storage_path)) {
                    Storage::makeDirectory($storage_path);
                }

                // Generate unique filename
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . uniqid() . '.' . $extension;
                
                // Store file in public/storage/library-files
                $path = $file->storeAs('library-files', $filename, 'public');
                
                if (!$path) {
                    \Log::error('File storage failed', [
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize()
                    ]);
                    
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['file' => 'Failed to save the file. Please try again.']);
                }
                
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

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Upload failed: ' . $e->getMessage()])
                ->withInput();
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
                // If clicking the same reaction, remove it
                $existing->delete();
            } else {
                // If changing reaction type, update it
                $existing->update(['reaction_type' => $validated['reaction_type']]);
            }
        } else {
            // Create new reaction
            LibraryReaction::create([
                'user_id' => auth()->id(),
                'library_item_id' => $item->id,
                'reaction_type' => $validated['reaction_type']
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

        // Recalculate average rating
        $averageRating = $item->ratings()->avg('rating');
        $ratingCount = $item->ratings()->count();

        $item->update([
            'average_rating' => round($averageRating, 1),
            'rating_count' => $ratingCount
        ]);

        return response()->json([
            'success' => true,
            'average_rating' => number_format($averageRating, 1),
            'rating_count' => $ratingCount
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

    public function download(LibraryItem $item)
    {
        // Check if file exists
        if (!$item->file_path || !Storage::disk('public')->exists($item->file_path)) {
            return redirect()->back()
                ->with('error', 'File not found.');
        }

        // Get the original file name if stored, or use the path's basename
        $fileName = basename($item->file_path);

        // Return file download response
        return Storage::disk('public')->download($item->file_path, $fileName);
    }

    public function getMyResources()
    {
        $resources = LibraryItem::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'resources' => $resources
        ]);
    }

    public function updateResource(Request $request, LibraryItem $item)
    {
        // Check if user owns the resource
        if ($item->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category' => 'required|string|max:50',
            'subcategory' => 'required|string|max:50',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Resource updated successfully',
            'resource' => $item
        ]);
    }

    public function deleteResource(LibraryItem $item)
    {
        // Check if user owns the resource
        if ($item->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Delete file if exists
        if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
            Storage::disk('public')->delete($item->file_path);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Resource deleted successfully'
        ]);
    }
}


