<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

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
            'title' => 'Test Post', // Add this if your model requires it
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Try to update the post as user1
        $response = $this->actingAs($user1)->put('/community/' . $post->id . '/update', [
            'content' => 'Modified content'
        ]);

        $response->assertStatus(403);
        
        // Verify content wasn't changed
        $this->assertDatabaseHas('communities', [
            'id' => $post->id,
            'content' => 'Original content'
        ]);
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
        
        // Create a library resource owned by user2
        $resource = LibraryItem::create([
            'user_id' => $user2->id,
            'title' => 'Test Resource',
            'description' => 'Test Description',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Try to delete the resource as user1
        $response = $this->actingAs($user1)->delete('/library/' . $resource->id . '/delete');
        
        $response->assertStatus(403);
        
        // Verify resource still exists
        $this->assertDatabaseHas('library_items', ['id' => $resource->id]);
    }
}
