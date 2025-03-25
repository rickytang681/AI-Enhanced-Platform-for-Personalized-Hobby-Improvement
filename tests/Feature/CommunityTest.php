<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Community;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_community_post()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/community', [
            'title' => 'Test Post',
            'content' => 'Test Content',
            'post_type' => 'discussion',
            'tag' => 'general'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('communities', [
            'title' => 'Test Post',
            'user_id' => $user->id
        ]);
    }

    public function test_user_can_comment_on_post()
    {
        $user = User::factory()->create();
        $post = Community::factory()->create();
        $this->actingAs($user);

        $response = $this->post("/community/{$post->id}/comment", [
            'content' => 'Test Comment'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('community_comments', [
            'content' => 'Test Comment',
            'user_id' => $user->id
        ]);
    }
}