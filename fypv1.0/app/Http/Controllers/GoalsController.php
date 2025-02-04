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
        
        // Check if user has hobbies and convert to array
        $userHobbies = [];
        if ($user && $user->hobbies) {
            $userHobbies = array_filter(explode(',', $user->hobbies)); // Remove empty values
        }

        // Get user's goals
        $goals = Goal::where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->get();

        // If user has no hobbies, redirect with message
        if (empty($userHobbies)) {
            return redirect()->route('hobby')->with('error', 'Please select your hobbies first!');
        }

        return view('goal', compact('userHobbies', 'goals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'goal' => 'required|string|max:255',
            'hobby' => 'required|string',
            'deadline' => 'nullable|date',
        ]);

        Goal::create([
            'user_id' => auth()->id(),
            'hobby' => $validated['hobby'],
            'goal' => $validated['goal'],
            'deadline' => $validated['deadline'],
            'progress' => 0,
            'status' => 'in-progress',
        ]);

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