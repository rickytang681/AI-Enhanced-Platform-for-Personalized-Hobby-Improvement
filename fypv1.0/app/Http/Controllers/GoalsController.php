<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;

class GoalsController extends Controller
{
    public function create()
    {
        // Get the authenticated user
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Get user's goals with proper ordering
        $goals = Goal::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Check if user has hobbies and convert to array
        $userHobbies = [];
        if ($user->hobbies) {
            $userHobbies = array_filter(explode(',', $user->hobbies));
        }

        // Instead of redirecting, we'll show a message in the view
        $showHobbyWarning = empty($userHobbies);

        // Pass all variables to the view
        return view('goal', [
            'goals' => $goals,
            'userHobbies' => $userHobbies,
            'showHobbyWarning' => $showHobbyWarning
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'goal' => 'required|string|max:255',
            'hobbies' => 'required|array|min:1',
            'experience' => 'required|array|min:1',
            'deadline' => 'nullable|date|after:today',
            'notes' => 'nullable|string'
        ]);

        // Combine hobbies and experience levels
        $hobbies = array_map(function($hobby, $experience) {
            return [
                'name' => $hobby,
                'experience' => $experience
            ];
        }, $validated['hobbies'], $validated['experience']);

        // Create the goal with explicit user_id
        $goal = new Goal([
            'user_id' => auth()->id(),
            'hobbies' => $hobbies,
            'goal' => $validated['goal'],
            'deadline' => $validated['deadline'],
            'notes' => $request->notes,
            'progress' => 0,
            'status' => 'in-progress',
        ]);

        // Save the goal
        $goal->save();

        return redirect()->back()->with('success', 'Goal created successfully!');
    }


    // Add method to update progress
    public function updateProgress(Request $request, Goal $goal)
    {
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        $goal->progress = $validated['progress'];
        // Automatically update status if progress is 100%
        if ($goal->progress == 100) {
            $goal->status = 'completed';
        }
        $goal->save();

        return redirect()->back()->with('success', 'Progress updated successfully!');
    }
} 