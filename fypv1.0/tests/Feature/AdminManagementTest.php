<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Community;
use App\Models\LibraryItem;
use App\Models\LibraryComment;
use App\Models\CommunityComment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);

        // Create regular user
        $this->regularUser = User::factory()->create([
            'role' => 'user'
        ]);
    }

    public function test_non_admin_cannot_access_system_page()
    {
        $response = $this->actingAs($this->regularUser)
            ->get('/system');

        $response->assertStatus(403); // Assert Forbidden status
        // No need to check redirect since we expect a 403 forbidden response
    }

    public function test_admin_can_access_system_page()
    {
        $response = $this->actingAs($this->admin)
            ->get('/system');

        $response->assertStatus(200);
    }

    public function test_admin_can_delete_user()
    {
        $userToDelete = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/system/users/{$userToDelete->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_admin_can_delete_library_resource()
    {
        $resource = LibraryItem::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/system/resources/{$resource->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('library_items', ['id' => $resource->id]);
    }

    public function test_admin_can_delete_library_comment()
    {
        $resource = LibraryItem::factory()->create();
        $comment = LibraryComment::create([
            'user_id' => $this->regularUser->id,
            'library_item_id' => $resource->id,
            'content' => 'Test comment'
        ]);

        $response = $this->actingAs($this->admin)
            ->delete("/system/comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('library_comments', ['id' => $comment->id]);
    }

    public function test_admin_can_delete_community_post()
    {
        $post = Community::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/system/community-posts/{$post->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('communities', ['id' => $post->id]);
    }

    public function test_admin_can_delete_community_comment()
    {
        $post = Community::factory()->create();
        $comment = CommunityComment::create([
            'user_id' => $this->regularUser->id,
            'community_id' => $post->id,
            'content' => 'Test comment'
        ]);

        $response = $this->actingAs($this->admin)
            ->delete("/system/community-comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('community_comments', ['id' => $comment->id]);
    }

    public function test_regular_user_cannot_delete_users()
    {
        $userToDelete = User::factory()->create();

        $response = $this->actingAs($this->regularUser)
            ->delete("/system/users/{$userToDelete->id}");

        $response->assertStatus(403); // Assert Forbidden status
        $this->assertDatabaseHas('users', ['id' => $userToDelete->id]);
    }

    public function test_admin_can_delete_multiple_content_types()
    {
        // Create test data
        $user = User::factory()->create();
        $resource = LibraryItem::factory()->create();
        $post = Community::factory()->create();
        $libraryComment = LibraryComment::create([
            'user_id' => $user->id,
            'library_item_id' => $resource->id,
            'content' => 'Test comment'
        ]);
        $communityComment = CommunityComment::create([
            'user_id' => $user->id,
            'community_id' => $post->id,
            'content' => 'Test comment'
        ]);

        // Login as admin
        $this->actingAs($this->admin);

        // Delete in correct order to avoid foreign key constraints
        // First delete comments
        $this->delete("/system/community-comments/{$communityComment->id}")
            ->assertStatus(200);

        // Then delete posts and resources
        $this->delete("/system/community-posts/{$post->id}")
            ->assertStatus(200);
        $this->delete("/system/resources/{$resource->id}")
            ->assertStatus(200);

        // Finally delete the user
        $this->delete("/system/users/{$user->id}")
            ->assertStatus(200);

        // Verify all deletions
        $this->assertDatabaseMissing('community_comments', ['id' => $communityComment->id]);
        $this->assertDatabaseMissing('communities', ['id' => $post->id]);
        $this->assertDatabaseMissing('library_items', ['id' => $resource->id]);
        $this->assertDatabaseMissing('library_comments', ['id' => $libraryComment->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_actions_are_isolated()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/system/users/{$user1->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user1->id]);
        $this->assertDatabaseHas('users', ['id' => $user2->id]);
    }
}




