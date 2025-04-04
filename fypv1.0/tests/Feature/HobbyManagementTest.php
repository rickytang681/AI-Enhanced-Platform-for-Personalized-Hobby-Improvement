<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hobby;
use App\Models\Goal;
use App\Models\Milestone;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HobbyManagementTest extends TestCase
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

    public function test_user_can_create_new_hobby()
    {
        $response = $this->actingAs($this->user)->post('/hobbies', [
            'name' => 'Photography',
            'description' => 'Learning digital photography',
            'experience_level' => 'Beginner'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('hobbies', [
            'name' => 'Photography',
            'description' => 'Learning digital photography',
            'user_id' => $this->user->id
        ]);
    }

    public function test_user_can_set_and_update_goals()
    {
        // First, create the goal directly in the database to ensure it works
        $goal = Goal::create([
            'hobby_id' => $this->hobby->id,
            'user_id' => $this->user->id,
            'goal' => 'Master basic techniques',
            'deadline' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 'in-progress'
        ]);

        // Verify the goal was created
        $this->assertNotNull($goal);
        $this->assertDatabaseHas('goals', [
            'id' => $goal->id,
            'hobby_id' => $this->hobby->id,
            'goal' => 'Master basic techniques'
        ]);

        // Now try updating the goal
        $updateData = [
            'hobby_id' => $this->hobby->id,
            'goal' => 'Master advanced techniques',
            'deadline' => now()->addMonths(4)->format('Y-m-d'),
            'status' => 'in-progress'
        ];

        $response = $this->actingAs($this->user)
                        ->put("/goals/{$goal->id}", $updateData);
        
        $response->assertRedirect();

        // Verify the update
        $goal->refresh();
        $this->assertEquals('Master advanced techniques', $goal->goal);
    }

    public function test_user_can_complete_milestones()
    {
        // Create a goal
        $goal = Goal::create([
            'hobby_id' => $this->hobby->id,
            'user_id' => $this->user->id,
            'goal' => 'Test Goal',
            'status' => 'in-progress',
            'deadline' => now()->addMonth()
        ]);

        // Create a milestone
        $milestone = Milestone::create([
            'goal_id' => $goal->id,
            'description' => 'Test Milestone',
            'completed' => false,
            'due_date' => now()->addWeek()
        ]);

        // Verify initial state
        $this->assertFalse($milestone->completed);

        // Toggle milestone completion - use POST instead of PATCH since that's what the route accepts
        $response = $this->actingAs($this->user)
            ->post("/goals/{$goal->id}/milestones/{$milestone->id}/toggle");

        // If it fails, let's see the actual error
        if ($response->status() === 500) {
            dd($response->exception->getMessage());
        }

        $response->assertSuccessful();

        // Verify the milestone was toggled
        $milestone->refresh();
        $this->assertTrue($milestone->completed);
    }

    public function test_user_can_edit_and_delete_hobbies()
    {
        // Test editing hobby
        $response = $this->actingAs($this->user)->put("/hobbies/{$this->hobby->id}/update", [
            'name' => 'Updated Hobby',
            'description' => 'Updated Description',
            'experience_level' => 'Intermediate'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('hobbies', [
            'id' => $this->hobby->id,
            'name' => 'Updated Hobby',
            'experience_level' => 'Intermediate'
        ]);

        // Test deleting hobby
        $response = $this->actingAs($this->user)->delete("/hobbies/{$this->hobby->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('hobbies', [
            'id' => $this->hobby->id
        ]);
    }

    public function test_user_can_edit_and_delete_goals_and_milestones()
    {
        $goal = Goal::create([
            'hobby_id' => $this->hobby->id,
            'user_id' => $this->user->id,
            'goal' => 'Original Goal',
            'status' => 'in-progress',
            'deadline' => now()->addMonth()
        ]);

        $milestone = Milestone::create([
            'goal_id' => $goal->id,
            'description' => 'Original Milestone',
            'due_date' => now()->addWeek()
        ]);

        // Verify initial creation
        $this->assertDatabaseHas('milestones', [
            'id' => $milestone->id,
            'description' => 'Original Milestone'
        ]);

        // Update milestone
        $response = $this->actingAs($this->user)
            ->put("/goals/{$goal->id}/milestones/{$milestone->id}", [
                'description' => 'Updated Milestone',
                'due_date' => now()->addWeek()->format('Y-m-d')
            ]);

        $response->assertStatus(200);
        
        $milestone->refresh();
        $this->assertEquals('Updated Milestone', $milestone->description);

        // Delete milestone
        $response = $this->actingAs($this->user)
            ->delete("/goals/{$goal->id}/milestones/{$milestone->id}");

        $response->assertStatus(200);
        
        // Check for soft delete
        $this->assertDatabaseHas('milestones', [
            'id' => $milestone->id,
            'deleted_at' => now()
        ]);

        // Delete goal
        $response = $this->actingAs($this->user)
            ->delete("/goals/{$goal->id}");

        $response->assertRedirect();
        
        // Check for soft delete of goal
        $this->assertDatabaseHas('goals', [
            'id' => $goal->id,
            'deleted_at' => now()
        ]);
    }

    public function test_user_cannot_modify_other_users_hobbies()
    {
        $otherUser = User::factory()->create();
        $otherHobby = Hobby::create([
            'user_id' => $otherUser->id,
            'name' => 'Other User Hobby',
            'description' => 'Original Description',
            'experience_level' => 'Beginner'
        ]);

        $originalName = $otherHobby->name;

        // Try to update other user's hobby
        $response = $this->actingAs($this->user)->put("/hobbies/{$otherHobby->id}", [
            'name' => 'Unauthorized Update',
            'description' => 'This should not work',
            'experience_level' => 'Expert'
        ]);

        $response->assertStatus(302);
        
        $otherHobby->refresh();
        $this->assertEquals($originalName, $otherHobby->name);

        // Try to delete other user's hobby
        $response = $this->actingAs($this->user)
            ->delete("/hobbies/{$otherHobby->id}");
        
        $response->assertStatus(302);
        $this->assertDatabaseHas('hobbies', ['id' => $otherHobby->id]);
    }

    public function test_validation_rules_for_hobby_creation()
    {
        $response = $this->actingAs($this->user)->post('/hobbies', [
            'name' => '',
            'description' => '',
            'experience_level' => 'invalid_level'
        ]);

        $response->assertSessionHasErrors(['name', 'description', 'experience_level']);
    }

    public function test_user_can_track_progress()
    {
        // Create a hobby first
        $hobby = Hobby::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Hobby',
            'description' => 'Test Description'
        ]);

        // Create a goal
        $goal = Goal::create([
            'user_id' => $this->user->id,
            'hobby_id' => $hobby->id,
            'goal' => 'Test Goal',
            'deadline' => now()->addDays(30)->format('Y-m-d'),
            'progress' => 0,
            'status' => 'in-progress'
        ]);

        // Create two milestones
        $milestone1 = $goal->milestones()->create([
            'description' => 'First Milestone',
            'due_date' => now()->addDays(10),
            'completed' => false
        ]);

        $milestone2 = $goal->milestones()->create([
            'description' => 'Second Milestone',
            'due_date' => now()->addDays(20),
            'completed' => false
        ]);

        // Mark one milestone as completed (50% progress)
        $response = $this->actingAs($this->user)
            ->post("/goals/{$goal->id}/milestones/{$milestone1->id}/toggle");

        $response->assertSuccessful();
        
        $goal->refresh();
        $this->assertEquals(50, $goal->progress);
        $this->assertEquals('in-progress', $goal->status);
    }

    public function test_user_can_set_goal_deadlines()
    {
        $hobby = Hobby::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Hobby',
            'description' => 'Test Description'
        ]);

        // Create goal with required milestones
        $response = $this->actingAs($this->user)
            ->post('/goals', [
                'hobby_id' => $hobby->id,
                'goal' => 'Test Goal with Deadline',
                'deadline' => now()->addDays(30)->format('Y-m-d'),
                'milestones' => ['First milestone', 'Second milestone'],
                'milestone_dates' => [
                    now()->addDays(10)->format('Y-m-d'),
                    now()->addDays(20)->format('Y-m-d')
                ]
            ]);

        $response->assertRedirect();
        
        $createdGoal = Goal::where('goal', 'Test Goal with Deadline')->first();
        $this->assertNotNull($createdGoal);
        $this->assertEquals('Test Goal with Deadline', $createdGoal->goal);
        $this->assertEquals(now()->addDays(30)->format('Y-m-d'), $createdGoal->deadline->format('Y-m-d'));
        
        // Verify milestones were created
        $this->assertEquals(2, $createdGoal->milestones()->count());
    }
}

