<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\LibraryItem;
use App\Models\LibraryComment;
use App\Models\Community;
use App\Models\CommunityComment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $users = User::all();
        $resources = LibraryItem::all();
        $libraryComments = LibraryComment::with(['user', 'libraryItem'])->get();
        $communityPosts = Community::with('user')->get();
        $communityComments = CommunityComment::with(['user', 'community'])->get();
        
        return view('system', compact('users', 'resources', 'libraryComments', 
                                    'communityPosts', 'communityComments'));
    }

    public function addUser(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
                'role' => ['required', 'string', 'in:user,admin'],
            ]);

            Log::info('Attempting to create user with data:', ['email' => $validated['email'], 'role' => $validated['role']]);

            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->role = $validated['role'];
            $user->save();

            Log::info('User created successfully', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error when creating user', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating user', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error creating user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your own account'
                ], 403);
            }

            DB::beginTransaction();
            try {
                // Delete user's hobbies and related data
                $hobbies = $user->hobbies()->get();
                foreach ($hobbies as $hobby) {
                    // Delete goals and milestones
                    $goals = $hobby->goals()->get();
                    foreach ($goals as $goal) {
                        $goal->milestones()->delete();
                        $goal->delete();
                    }
                    $hobby->delete();
                }

                // Delete library interactions
                $user->libraryComments()->delete();
                $user->libraryReactions()->delete();
                $user->libraryRatings()->delete();
                $user->librarySaves()->delete();

                // Delete community interactions
                $user->communityPosts()->delete();
                $user->communityComments()->delete();
                $user->communityReactions()->delete();
                
                // Delete community saved posts
                $user->communitySavedPosts()->detach();

                // Finally, delete the user
                $user->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'User and all related data deleted successfully'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteResource(LibraryItem $resource)
    {
        try {
            // Delete the associated file if it exists
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }

            // Delete associated records
            $resource->reactions()->delete();
            $resource->comments()->delete();
            $resource->ratings()->delete();
            $resource->saves()->delete();
            
            // Delete the resource
            $resource->delete();

            return response()->json([
                'success' => true,
                'message' => 'Resource deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting resource', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting resource'
            ], 500);
        }
    }

    public function deleteComment(LibraryComment $comment)
    {
        try {
            $comment->delete();
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting comment', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting comment'
            ], 500);
        }
    }

    public function deleteCommunityPost(Community $post)
    {
        try {
            // Delete the cover image if it exists
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }

            // Delete associated records
            $post->reactions()->delete();
            $post->comments()->delete();
            $post->savedByUsers()->detach();
            
            // Delete the post
            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Community post deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting community post', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting community post'
            ], 500);
        }
    }

    public function deleteCommunityComment(CommunityComment $comment)
    {
        try {
            $comment->delete();
            return response()->json([
                'success' => true,
                'message' => 'Community comment deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting community comment', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting community comment'
            ], 500);
        }
    }
}
