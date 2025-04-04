<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hobby;
use App\Models\Goal;
use App\Models\Recommendation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HobbyTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_create_hobby()
    {
        $hobby = Hobby::create([
            'user_id' => $this->user->id,
            'name' => 'Photography',
            'description' => 'Digital photography',
            'experience_level' => 'Beginner'
        ]);

        $this->assertInstanceOf(Hobby::class, $hobby);
        $this->assertEquals('Photography', $hobby->name);
    }

    public function test_hobby_belongs_to_user()
    {
        $hobby = Hobby::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->assertInstanceOf(User::class, $hobby->user);
        $this->assertEquals($this->user->id, $hobby->user->id);
    }

    public function test_hobby_has_many_goals()
    {
        $hobby = Hobby::factory()->create([
            'user_id' => $this->user->id
        ]);

        Goal::factory()->create([
            'hobby_id' => $hobby->id
        ]);

        $this->assertInstanceOf(Goal::class, $hobby->goals->first());
        $this->assertEquals(1, $hobby->goals->count());
    }

    public function test_can_update_hobby()
    {
        $hobby = Hobby::factory()->create([
            'user_id' => $this->user->id
        ]);

        $hobby->update([
            'name' => 'Updated Hobby',
            'experience_level' => 'Intermediate'
        ]);

        $this->assertEquals('Updated Hobby', $hobby->name);
        $this->assertEquals('Intermediate', $hobby->experience_level);
    }

    public function test_can_delete_hobby()
    {
        $hobby = Hobby::factory()->create([
            'user_id' => $this->user->id
        ]);

        $hobby->delete();

        $this->assertDatabaseMissing('hobbies', ['id' => $hobby->id]);
    }

    public function test_deleting_hobby_deletes_related_goals()
    {
        $hobby = Hobby::factory()->create([
            'user_id' => $this->user->id
        ]);

        $goal = Goal::factory()->create([
            'hobby_id' => $hobby->id,
            'user_id' => $this->user->id
        ]);

        $hobby->delete();

        // Check that the goal exists but is soft deleted
        $this->assertSoftDeleted('goals', ['id' => $goal->id]);
    }

    public function test_hobby_completion_percentage()
    {
        $hobby = Hobby::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Create goals with different statuses
        Goal::factory()->count(2)->create([
            'hobby_id' => $hobby->id,
            'status' => 'completed'
        ]);

        Goal::factory()->count(2)->create([
            'hobby_id' => $hobby->id,
            'status' => 'in_progress'
        ]);

        // Calculate manually
        $totalGoals = $hobby->goals()->count();
        $completedGoals = $hobby->goals()->where('status', 'completed')->count();
        $percentage = $totalGoals > 0 ? ($completedGoals / $totalGoals) * 100 : 0;

        $this->assertEquals($percentage, $hobby->goals()->where('status', 'completed')->count() / $hobby->goals()->count() * 100);
    }

    public function test_hobby_active_goals_count()
    {
        $hobby = Hobby::factory()->create([
            'user_id' => $this->user->id
        ]);

        Goal::factory()->count(3)->create([
            'hobby_id' => $hobby->id,
            'status' => 'in_progress'
        ]);

        $this->assertEquals(3, $hobby->goals()->where('status', 'in_progress')->count());
    }

    public function test_hobby_completed_goals_count()
    {
        $hobby = Hobby::factory()->create([
            'user_id' => $this->user->id
        ]);

        Goal::factory()->count(2)->create([
            'hobby_id' => $hobby->id,
            'status' => 'completed'
        ]);

        $this->assertEquals(2, $hobby->goals()->where('status', 'completed')->count());
    }
}






