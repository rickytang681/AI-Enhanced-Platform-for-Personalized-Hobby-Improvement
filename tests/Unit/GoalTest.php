<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Goal;
use App\Models\Milestone;
use App\Models\User;

class GoalTest extends TestCase
{
    public function test_goal_progress_tracking()
    {
        $goal = Goal::factory()->create([
            'progress' => 0
        ]);

        $goal->progress = 50;
        $goal->save();

        $this->assertEquals(50, $goal->fresh()->progress);
    }

    public function test_goal_has_milestones()
    {
        $goal = Goal::factory()->create();
        $milestone = Milestone::factory()->create([
            'goal_id' => $goal->id
        ]);

        $this->assertTrue($goal->milestones->contains($milestone));
    }
}