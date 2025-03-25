<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Community;
use App\Models\LibraryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_protected_pages()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    public function test_guest_redirects_work_correctly()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_user_cannot_view_another_users_profile()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->get('/profile');
        $response->assertStatus(200); // Verify user can access their own profile

        // Verify accessing another user's profile is not possible
        $response = $this->actingAs($user1)->get('/profile/' . $user2->id);
        $response->assertStatus(404); // Route doesn't exist, so 404 is correct
    }

    public function test_user_cannot_edit_others_community_post()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a post owned by user2
        $post = Community::create([
            'user_id' => $user2->id,
            'content' => 'Original content',
            'title' => 'Test Post',
            'post_type' => 'discussion', // Add required field
            'tag' => 'General', // Add required field
            'created_at' => now(),
        ]);

        // Attempt to edit the post as user1
        $response = $this->actingAs($user1)->putJson("/community/{$post->id}/update", [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'post_type' => 'question',
            'tag' => 'Help'
        ]);

        $response->assertForbidden();
    }

    public function test_unauthorized_api_access()
    {
        // Test without any authentication
        $response = $this->getJson('/api/user');
        $response->assertStatus(401);

        // Test with invalid authorization header
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token',
        ])->getJson('/api/user');
        $response->assertStatus(401);
    }

    public function test_user_cannot_delete_others_library_resource()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Use the factory to create a library resource
        $resource = LibraryItem::factory()->create([
            'user_id' => $user2->id
        ]);

        // Attempt to delete the resource as user1
        $response = $this->actingAs($user1)
            ->delete("/library/{$resource->id}/delete");

        $response->assertForbidden();
    }
}
