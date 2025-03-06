<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MilestoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Goal $goal)
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

        $milestone = $goal->milestones()->create($validated);

        return redirect()->route('goals.index')
            ->with('success', 'Milestone added successfully')
            ->with('activeTab', 'my-goals');
    }

    public function update(Request $request, Goal $goal, Milestone $milestone)
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

        $milestone->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Milestone updated successfully'
        ]);
    }

    public function destroy(Goal $goal, Milestone $milestone)
    {
        try {
            // Authorize the action
            $this->authorize('delete', $milestone);
            
            DB::beginTransaction();
            
            // Verify that the milestone belongs to the goal
            if ($milestone->goal_id !== $goal->id) {
                throw new \Exception('Invalid milestone for this goal');
            }

            $milestone->delete();
            
            // Recalculate goal progress
            $totalMilestones = $goal->milestones()->count();
            $completedMilestones = $goal->milestones()->where('completed', true)->count();
            $progress = $totalMilestones > 0 ? round(($completedMilestones / $totalMilestones) * 100) : 0;
            
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

    public function toggle(Goal $goal, Milestone $milestone)
    {
        try {
            $milestone->update(['completed' => request('completed')]);
            
            // Recalculate goal progress
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update milestone status'
            ], 500);
        }
    }
}
