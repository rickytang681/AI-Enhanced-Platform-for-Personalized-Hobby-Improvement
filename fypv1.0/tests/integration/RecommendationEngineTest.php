<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hobby;
use App\Models\Goal;
use App\Models\Recommendation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class RecommendationEngineTest extends TestCase
{
    use RefreshDatabase;
    
    protected $user;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    
    public function test_recommendation_generation_based_on_hobbies()
    {
        // Create a hobby for the user
        $hobby = Hobby::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Photography',
            'description' => 'Learning digital photography',
            'experience_level' => 'Beginner'
        ]);
        
        // Create a goal for the hobby
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'hobby_id' => $hobby->id,
            'goal' => 'Learn Photography Basics',
            'deadline' => now()->addMonths(3),
            'progress' => 0,
            'status' => 'in-progress'
        ]);
        
        // Mock the API response
        Http::fake([
            '*' => Http::response([
                'status' => true,
                'result' => 'Test recommendation content'
            ], 200)
        ]);
        
        // Make the request to generate recommendations
        $response = $this->actingAs($this->user)
                         ->post('/recommendation/get', [
                             'selected_hobbies' => [$hobby->id],
                             'selected_goals' => [$goal->id]
                         ]);
        
        // Check if the request was successful
        $response->assertStatus(200);
        
        // Verify the response structure
        $response->assertJsonStructure([
            'success',
            'recommendation_id',
            'recommendations'
        ]);
        
        // Verify that recommendations were created in the database
        $this->assertDatabaseHas('recommendations', [
            'user_id' => $this->user->id,
            'hobby_id' => $hobby->id,
            'goal_id' => $goal->id
        ]);
        
        // Get the created recommendation
        $recommendation = Recommendation::where('user_id', $this->user->id)
                                       ->where('hobby_id', $hobby->id)
                                       ->first();
        
        // Verify that the recommendation has content
        $this->assertNotNull($recommendation);
        $this->assertNotEmpty($recommendation->content);
    }
}






