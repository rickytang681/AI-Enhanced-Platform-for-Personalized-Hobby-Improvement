<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $goals = Goal::where('user_id', $user->id)
                    ->with('milestones')
                    ->orderBy('created_at', 'desc')
                    ->get();

        $userHobbies = [];
        if ($user->hobbies) {
            $userHobbies = array_filter(explode(',', $user->hobbies));
        }

        return view('goal', [
            'goals' => $goals,
            'userHobbies' => $userHobbies,
            'showHobbyWarning' => empty($userHobbies)
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

        $hobbies = array_map(function($hobby, $experience) {
            return [
                'name' => $hobby,
                'experience' => $experience
            ];
        }, $validated['hobbies'], $validated['experience']);

        $goal = Goal::create([
            'user_id' => auth()->id(),
            'hobbies' => $hobbies,
            'goal' => $validated['goal'],
            'deadline' => $validated['deadline'],
            'notes' => $request->notes,
            'progress' => 0,
            'status' => 'in-progress',
        ]);

        return redirect()->back()->with('success', 'Goal created successfully!');
    }

    public function addMilestone(Request $request, Goal $goal)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        $goal->milestones()->create($validated);

        return back()->with('success', 'Milestone added successfully');
    }

    public function toggleMilestone(Request $request, Goal $goal, $milestoneId)
    {
        $milestone = $goal->milestones()->findOrFail($milestoneId);
        $milestone->update(['completed' => $request->completed]);

        // Calculate new progress based on completed milestones
        $totalMilestones = $goal->milestones()->count();
        $completedMilestones = $goal->milestones()->where('completed', true)->count();
        
        $progress = $totalMilestones > 0 ? round(($completedMilestones / $totalMilestones) * 100) : 0;
        
        // Update goal progress and status
        $goal->update([
            'progress' => $progress,
            'status' => $progress == 100 ? 'completed' : 'in-progress'
        ]);

        return response()->json([
            'success' => true,
            'progress' => $progress,
            'status' => $goal->status
        ]);
    }

    public function destroy(Goal $goal)
    {
        $goal->delete();
        return redirect()->route('goals.index')->with('success', 'Goal deleted successfully.');
    }
} 