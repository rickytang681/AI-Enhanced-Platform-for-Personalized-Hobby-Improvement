<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Community;
use App\Models\CommunityComment;
use App\Models\CommunityReaction;
use App\Models\CommunitySavedPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CommunityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    public function test_user_can_create_post()
    {
        $postData = [
            'title' => 'Test Post Title',
            'content' => 'Test post content',
            'post_type' => 'discussion',
            'tag' => 'General',
            'cover_image' => UploadedFile::fake()->image('cover.jpg')
        ];

        $response = $this->actingAs($this->user)
            ->post('/community', $postData);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Post created successfully!');

        $this->assertDatabaseHas('communities', [
            'user_id' => $this->user->id,
            'title' => $postData['title'],
            'content' => $postData['content'],
            'post_type' => $postData['post_type'],
            'tag' => $postData['tag']
        ]);
    }

    public function test_user_can_view_own_posts()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id,
            'post_type' => 'discussion',
            'tag' => 'General'
        ]);

        $response = $this->actingAs($this->user)
            ->get('/community/my-posts');

        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    public function test_user_can_react_to_post()
    {
        $post = Community::factory()->create([
            'user_id' => $this->otherUser->id,
            'post_type' => 'discussion',
            'tag' => 'General'
        ]);

        $response = $this->actingAs($this->user)
            ->post("/community/{$post->id}/react", [
                'reaction_type' => 'like'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('community_reactions', [
            'user_id' => $this->user->id,
            'community_id' => $post->id,
            'reaction_type' => 'like'
        ]);
    }

    public function test_user_can_comment_on_post()
    {
        $post = Community::factory()->create([
            'user_id' => $this->otherUser->id,
            'post_type' => 'discussion',
            'tag' => 'General'
        ]);

        $response = $this->actingAs($this->user)
            ->post("/community/{$post->id}/comment", [
                'content' => 'Test comment'
            ]);

        $response->assertStatus(200); // Changed from 302 to 200 as it's an AJAX request

        $this->assertDatabaseHas('community_comments', [
            'user_id' => $this->user->id,
            'community_id' => $post->id,
            'content' => 'Test comment'
        ]);
    }

    public function test_user_can_edit_own_post()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id,
            'post_type' => 'discussion',
            'tag' => 'General'
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'post_type' => 'question',
            'tag' => 'Help'
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/community/{$post->id}/update", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('communities', [
            'id' => $post->id,
            'title' => $updateData['title'],
            'content' => $updateData['content'],
            'post_type' => $updateData['post_type'],
            'tag' => $updateData['tag']
        ]);
    }

    public function test_user_cannot_edit_others_post()
    {
        $post = Community::factory()->create([
            'user_id' => $this->otherUser->id,
            'post_type' => 'discussion',
            'tag' => 'General'
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'post_type' => 'question',
            'tag' => 'Help',
            '_token' => csrf_token()
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/community/{$post->id}/update", $updateData, [
                'Accept' => 'application/json',
                'X-CSRF-TOKEN' => csrf_token()
            ]);

        $response->assertForbidden();
    }

    public function test_user_can_delete_own_post()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id,
            'post_type' => 'discussion',
            'tag' => 'General'
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/community/{$post->id}");

        $response->assertStatus(200); // Changed from 302 to 200 as it's an AJAX request
        $this->assertDatabaseMissing('communities', ['id' => $post->id]);
    }

    public function test_user_cannot_delete_others_post()
    {
        $post = Community::factory()->create([
            'user_id' => $this->otherUser->id,
            'post_type' => 'discussion',
            'tag' => 'General'
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/community/{$post->id}");

        $response->assertStatus(403);
    }

    protected function tearDown(): void
    {
        Storage::fake('public')->deleteDirectory('community-images');
        parent::tearDown();
    }
}






