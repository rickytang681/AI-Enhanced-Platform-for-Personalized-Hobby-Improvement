<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hobby;
use App\Models\Goal;
use App\Models\Recommendation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecommendationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $hobby;
    protected $goal;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->hobby = Hobby::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Programming',
            'experience_level' => 'Beginner'
        ]);
        $this->goal = Goal::factory()->create([
            'hobby_id' => $this->hobby->id,
            'goal' => 'Learn Laravel',
            'status' => 'in_progress'
        ]);
    }

    public function test_can_create_recommendation()
    {
        $recommendation = Recommendation::create([
            'user_id' => $this->user->id,
            'hobby_id' => $this->hobby->id,
            'goal_id' => $this->goal->id,
            'content' => 'Test recommendation'
        ]);

        $this->assertInstanceOf(Recommendation::class, $recommendation);
        $this->assertEquals($this->user->id, $recommendation->user_id);
    }

    public function test_recommendation_belongs_to_user()
    {
        $recommendation = Recommendation::create([
            'user_id' => $this->user->id,
            'hobby_id' => $this->hobby->id,
            'goal_id' => $this->goal->id,
            'content' => 'Test recommendation'
        ]);

        $this->assertInstanceOf(User::class, $recommendation->user);
    }

    public function test_recommendation_belongs_to_hobby()
    {
        $recommendation = Recommendation::create([
            'user_id' => $this->user->id,
            'hobby_id' => $this->hobby->id,
            'goal_id' => $this->goal->id,
            'content' => 'Test recommendation'
        ]);

        $this->assertInstanceOf(Hobby::class, $recommendation->hobby);
    }

    public function test_recommendation_belongs_to_goal()
    {
        $recommendation = Recommendation::create([
            'user_id' => $this->user->id,
            'hobby_id' => $this->hobby->id,
            'goal_id' => $this->goal->id,
            'content' => 'Test recommendation'
        ]);

        $this->assertInstanceOf(Goal::class, $recommendation->goal);
    }

    public function test_can_delete_recommendation()
    {
        $recommendation = Recommendation::create([
            'user_id' => $this->user->id,
            'hobby_id' => $this->hobby->id,
            'goal_id' => $this->goal->id,
            'content' => 'Test recommendation'
        ]);

        $recommendation->delete();

        $this->assertDatabaseMissing('recommendations', ['id' => $recommendation->id]);
    }
}
