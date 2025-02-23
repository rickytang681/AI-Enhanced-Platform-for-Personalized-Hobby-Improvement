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

        // Apply search filter
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Apply category filter
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Apply subcategory filter
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

        $items = $query->paginate(10);

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
                'description' => 'required|string',
                'category' => 'required|string',
                'subcategory' => 'required|string',
                'type' => 'required|in:video,text',
                'content' => 'required_if:type,text',
                'video_url' => 'required_if:type,video|url|nullable',
                'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,txt,mp4,zip,rar',
            ]);

            \Log::info('Validation passed', $validated); // Add logging

            if ($request->hasFile('file')) {
                try {
                    $filename = time() . '_' . $request->file('file')->getClientOriginalName();
                    $path = $request->file('file')->storeAs('library-files', $filename, 'public');
                    $validated['file_path'] = $path;
                    \Log::info('File uploaded successfully', ['path' => $path]); // Add logging
                } catch (\Exception $e) {
                    \Log::error('File upload failed', ['error' => $e->getMessage()]); // Add logging
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['file' => 'Failed to upload file: ' . $e->getMessage()]);
                }
            }

            $validated['user_id'] = auth()->id();
            $validated['likes'] = 0;
            $validated['dislikes'] = 0;

            // Remove null values from validated data
            $validated = array_filter($validated, function($value) {
                return !is_null($value);
            });

            \Log::info('Creating library item', $validated); // Add logging

            $item = LibraryItem::create($validated);

            \Log::info('Library item created', ['item_id' => $item->id]); // Add logging

            return redirect()->route('library')
                ->with('success', 'Resource uploaded successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to create library item', ['error' => $e->getMessage()]); // Add logging
            
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create resource: ' . $e->getMessage()]);
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
        return [
            'Photography', 'Coding', 'Reading', 'Video Games',
            'Writing', 'Music', 'Sports', 'Cooking',
            'Gardening', 'Arts', 'Crafts', 'Running'
        ];
    }

    private function getSubcategories()
    {
        return [
            'Beginner',
            'Intermediate',
            'Advanced'
        ];
    }
} 