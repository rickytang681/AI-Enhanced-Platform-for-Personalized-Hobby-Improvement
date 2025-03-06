<?php

namespace App\Http\Controllers;

use App\Models\Hobby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HobbyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $hobbies = auth()->user()->hobbies()->orderBy('created_at', 'desc')->get();
        return view('hobbies.index', compact('hobbies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'experience_level' => 'required|in:Beginner,Intermediate,Expert',
        ]);

        $hobby = auth()->user()->hobbies()->create($validated);

        return redirect()->route('hobbies.index')
            ->with('success', 'Hobby created successfully! Would you like to set some goals for this hobby?')
            ->with('new_hobby_id', $hobby->id);
    }

    public function edit(Hobby $hobby)
    {
        return view('hobbies.edit', compact('hobby'));
    }

    public function update(Request $request, Hobby $hobby)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'experience_level' => 'required|in:Beginner,Intermediate,Expert',
        ]);

        $hobby->update($validated);

        return redirect()->route('hobbies.index')
            ->with('success', 'Hobby updated successfully!');
    }

    public function destroy(Hobby $hobby)
    {
        try {
            // Check if the authenticated user owns this hobby
            if ($hobby->user_id !== auth()->id()) {
                return redirect()->route('hobbies.index')
                    ->with('error', 'Unauthorized action.');
            }

            DB::beginTransaction();
            try {
                // The boot method in the model will handle cascading deletes
                $hobby->delete();
                DB::commit();

                return redirect()->route('hobbies.index')
                    ->with('success', 'Hobby and all associated goals and milestones deleted successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return redirect()->route('hobbies.index')
                ->with('error', 'Failed to delete hobby. Please try again.');
        }
    }
}
