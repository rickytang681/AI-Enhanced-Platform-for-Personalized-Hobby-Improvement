<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hobby;
use App\Models\Goal;
use App\Models\Recommendation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

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
        $this->hobby = Hobby::factory()->create(['user_id' => $this->user->id]);
        $this->goal = Goal::factory()->create(['hobby_id' => $this->hobby->id]);
    }

    public function test_user_can_view_recommendations()
    {
        $recommendation = Recommendation::create([
            'user_id' => $this->user->id,
            'hobby_id' => $this->hobby->id,
            'goal_id' => $this->goal->id,
            'content' => "Test recommendation content"
        ]);

        $response = $this->actingAs($this->user)
            ->get('/recommendation');

        $response->assertStatus(200);
        $response->assertViewHas('recommendations');
    }

    public function test_user_can_delete_recommendation()
    {
        $recommendation = Recommendation::create([
            'user_id' => $this->user->id,
            'hobby_id' => $this->hobby->id,
            'goal_id' => $this->goal->id,
            'content' => "Test recommendation content"
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/recommendations/{$recommendation->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('recommendations', ['id' => $recommendation->id]);
    }

    public function test_user_cannot_generate_recommendation_without_hobby()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/recommendation/get', [
                'selected_hobbies' => [],
                'selected_goals' => [$this->goal->id]
            ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['selected_hobbies']);
    }

    public function test_user_cannot_access_other_users_recommendations()
    {
        $otherUser = User::factory()->create();
        $otherHobby = Hobby::factory()->create(['user_id' => $otherUser->id]);
        $otherGoal = Goal::factory()->create(['hobby_id' => $otherHobby->id]);
        
        $recommendation = Recommendation::create([
            'user_id' => $otherUser->id,
            'hobby_id' => $otherHobby->id,
            'goal_id' => $otherGoal->id,
            'content' => "Other user's recommendation"
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/recommendations/{$recommendation->id}");

        $this->assertDatabaseHas('recommendations', ['id' => $recommendation->id]);
        $response->assertStatus(403);
    }

    public function test_user_can_generate_recommendation()
    {
        $mockRecommendationContent = "Test recommendation content";
        
        // Mock the API response
        Http::fake([
            '*' => Http::response([
                'status' => true,
                'result' => $mockRecommendationContent
            ], 200)
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/recommendation/get', [
                'selected_hobbies' => [$this->hobby->id],
                'selected_goals' => [$this->goal->id]
            ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'recommendations' => [], // Changed to array notation
                     'recommendation_id'
                 ]);

        // Additional assertions to verify the response content
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertNotNull($responseData['recommendation_id']);
        $this->assertNotEmpty($responseData['recommendations']);
    }
}




