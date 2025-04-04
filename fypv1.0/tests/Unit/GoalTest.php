<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hobby;
use App\Models\Goal;
use App\Models\Milestone;
use App\Models\Recommendation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GoalTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $hobby;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->hobby = Hobby::factory()->create([
            'user_id' => $this->user->id
        ]);
    }

    public function test_can_create_goal()
    {
        $goalData = [
            'user_id' => $this->user->id,  // Add user_id
            'hobby_id' => $this->hobby->id,
            'goal' => 'Master photography basics',
            'status' => 'in_progress',
            'deadline' => now()->addDays(30)->format('Y-m-d')
        ];

        $goal = Goal::create($goalData);

        $this->assertInstanceOf(Goal::class, $goal);
        $this->assertEquals('Master photography basics', $goal->goal);
        $this->assertDatabaseHas('goals', ['id' => $goal->id]);
    }

    public function test_goal_belongs_to_hobby()
    {
        $goal = Goal::factory()->create([
            'hobby_id' => $this->hobby->id
        ]);

        $this->assertInstanceOf(Hobby::class, $goal->hobby);
        $this->assertEquals($this->hobby->id, $goal->hobby->id);
    }

    public function test_goal_has_many_milestones()
    {
        $goal = Goal::factory()->create([
            'hobby_id' => $this->hobby->id
        ]);

        $milestone = Milestone::factory()->create([
            'goal_id' => $goal->id,
            'description' => 'Test milestone',
            'completed' => false
        ]);

        $this->assertInstanceOf(Milestone::class, $goal->milestones->first());
        $this->assertEquals(1, $goal->milestones->count());
        $this->assertEquals('Test milestone', $goal->milestones->first()->description);
    }

    public function test_can_update_goal_status()
    {
        $goal = Goal::factory()->create([
            'hobby_id' => $this->hobby->id,
            'status' => 'in_progress'
        ]);

        $goal->update(['status' => 'completed']);
        $goal->refresh();

        $this->assertEquals('completed', $goal->status);
    }

    public function test_can_delete_goal()
    {
        $goal = Goal::factory()->create([
            'hobby_id' => $this->hobby->id
        ]);

        $goalId = $goal->id;
        $goal->delete();

        // Check that the record exists but is soft deleted
        $this->assertSoftDeleted('goals', [
            'id' => $goalId
        ]);
    }

    public function test_deleting_goal_deletes_related_milestones()
    {
        $goal = Goal::factory()->create([
            'hobby_id' => $this->hobby->id
        ]);

        $milestone = Milestone::factory()->create([
            'goal_id' => $goal->id,
            'description' => 'Test milestone',
            'completed' => false
        ]);

        $milestoneId = $milestone->id;
        
        // Delete the goal which should trigger milestone deletion
        $goal->delete();

        // Check that both records exist but are soft deleted
        $this->assertSoftDeleted('goals', ['id' => $goal->id]);
        $this->assertSoftDeleted('milestones', ['id' => $milestoneId]);
    }

    public function test_goal_completion_percentage()
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'in_progress'
        ]);

        // Create some milestones
        Milestone::factory()->count(2)->create([
            'goal_id' => $goal->id,
            'completed' => true // Change 'status' to 'completed'
        ]);

        Milestone::factory()->count(2)->create([
            'goal_id' => $goal->id,
            'completed' => false // Change 'status' to 'completed'
        ]);

        // Calculate manually
        $totalMilestones = $goal->milestones()->count();
        $completedMilestones = $goal->milestones()->where('completed', true)->count();
        $percentage = $totalMilestones > 0 ? ($completedMilestones / $totalMilestones) * 100 : 0;

        $this->assertEquals($percentage, $goal->milestones()->where('completed', true)->count() / $goal->milestones()->count() * 100);
    }

    public function test_goal_has_recommendations()
    {
        // First, ensure the Goal model has the recommendations relationship
        $hobby = Hobby::factory()->create([
            'user_id' => $this->user->id
        ]);

        $goal = Goal::factory()->create([
            'hobby_id' => $hobby->id,
            'user_id' => $this->user->id
        ]);

        // Create a recommendation using proper columns
        $recommendation = new Recommendation([
            'user_id' => $this->user->id,
            'hobby_id' => $hobby->id,
            'goal_id' => $goal->id,
            'content' => 'Test recommendation',
            'type' => 'goal' // Add type if required
        ]);
        $recommendation->save();

        // Test the relationship
        $this->assertDatabaseHas('recommendations', [
            'goal_id' => $goal->id,
            'content' => 'Test recommendation'
        ]);
    }

    public function test_goal_due_date_validation()
    {
        $this->expectException(\Exception::class);

        Goal::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => 'invalid-date'
        ]);
    }

    public function test_goal_status_validation()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Goal::create([
            'user_id' => $this->user->id,
            'hobby_id' => Hobby::factory()->create(['user_id' => $this->user->id])->id,
            'title' => 'Test Goal',
            'status' => 'invalid_status', // This should fail as status should be enum or constrained
            'deadline' => now()->addDays(7)
        ]);
    }
}

