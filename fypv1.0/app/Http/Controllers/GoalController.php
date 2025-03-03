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
            'deadline' => 'required|date|after:today',
            'milestones' => 'required|array|min:1',
            'milestone_dates' => 'required|array|min:1',
            'milestone_dates.*' => 'required|date|after_or_equal:today'
        ]);

        // Validate milestone dates against goal deadline
        foreach ($validated['milestone_dates'] as $date) {
            if (strtotime($date) > strtotime($validated['deadline'])) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['milestone_dates' => 'Milestone dates cannot be later than the goal deadline']);
            }
        }

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
            'progress' => 0,
            'status' => 'in-progress',
        ]);

        // Create milestones
        foreach ($validated['milestones'] as $index => $milestone) {
            $goal->milestones()->create([
                'description' => $milestone,
                'due_date' => $validated['milestone_dates'][$index],
                'completed' => false
            ]);
        }

        return redirect()->route('goals.index')
            ->with('success', 'Goal created successfully!')
            ->with('activeTab', 'my-goals'); // Add this to switch to My Goals tab
    }

    public function addMilestone(Request $request, Goal $goal)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'due_date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) use ($goal) {
                    if (strtotime($value) > strtotime($goal->deadline)) {
                        $fail('Milestone date cannot be later than the goal deadline.');
                    }
                },
            ],
        ]);

        $goal->milestones()->create($validated);

        return redirect()->route('goals.index')
            ->with('success', 'Milestone added successfully')
            ->with('activeTab', 'my-goals');
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