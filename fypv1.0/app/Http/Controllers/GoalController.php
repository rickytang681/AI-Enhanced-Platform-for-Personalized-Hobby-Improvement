<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Hobby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $hobbies = $user->hobbies;
        
        if ($hobbies->isEmpty()) {
            return redirect()->route('hobbies.index')
                ->with('warning', 'Please create at least one hobby before setting goals.');
        }

        $goals = Goal::where('user_id', $user->id)
                    ->with(['milestones', 'hobby'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Group goals by hobby
        $goalsByHobby = $goals->groupBy('hobby_id');
        
        // Find hobbies without goals
        $hobbiesWithoutGoals = $hobbies->whereNotIn('id', $goals->pluck('hobby_id')->unique());

        // Get the selected hobby_id from the request
        $selectedHobbyId = $request->query('hobby_id');

        return view('goal', [
            'goals' => $goals,
            'hobbies' => $hobbies,
            'goalsByHobby' => $goalsByHobby,
            'hobbiesWithoutGoals' => $hobbiesWithoutGoals,
            'selectedHobbyId' => $selectedHobbyId
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hobby_id' => 'required|exists:hobbies,id',
            'goal' => 'required|string|max:255',
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

        $goal = Goal::create([
            'user_id' => auth()->id(),
            'hobby_id' => $validated['hobby_id'],
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
            ->with('activeTab', 'my-goals');
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
        try {
            // Authorize the action
            $this->authorize('delete', $goal);

            DB::beginTransaction();
            
            $goal->delete();
            
            DB::commit();
            
            return redirect()->route('goals.index')
                ->with('success', 'Goal and all associated milestones deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('goals.index')
                ->with('error', 'Failed to delete goal. Please try again.');
        }
    }

    public function update(Request $request, Goal $goal)
    {
        // Authorize the action
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'hobby_id' => 'required|exists:hobbies,id',
            'goal' => 'required|string|max:255',
            'deadline' => 'required|date|after:today',
        ]);

        $goal->update($validated);

        return redirect()->route('goals.index')
            ->with('success', 'Goal updated successfully!')
            ->with('activeTab', 'my-goals');
    }

    public function updateMilestone(Request $request, Goal $goal, $milestoneId)
    {
        // Authorize the action
        $this->authorize('manage', $goal);

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

        $milestone = $goal->milestones()->findOrFail($milestoneId);
        $milestone->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Milestone updated successfully'
        ]);
    }

    public function deleteMilestone(Goal $goal, $milestoneId)
    {
        try {
            DB::beginTransaction();
            
            $milestone = $goal->milestones()->findOrFail($milestoneId);
            $milestone->delete();

            // Recalculate goal progress
            $totalMilestones = $goal->milestones()->count();
            $completedMilestones = $goal->milestones()->where('completed', true)->count();
            
            // Calculate new progress
            $progress = $totalMilestones > 0 ? round(($completedMilestones / $totalMilestones) * 100) : 0;
            
            // Update goal progress and status
            $goal->update([
                'progress' => $progress,
                'status' => $progress == 100 ? 'completed' : 'in-progress'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'progress' => $progress,
                'status' => $goal->status
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete milestone'
            ], 500);
        }
    }
}

