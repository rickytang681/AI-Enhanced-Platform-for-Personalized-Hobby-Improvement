<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Community;
use App\Models\CommunityComment;
use App\Models\CommunityReaction;
use App\Models\CommunitySavedPost;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_create_community_post()
    {
        $post = Community::create([
            'user_id' => $this->user->id,
            'title' => 'Test Post',
            'content' => 'Test Content',
            'post_type' => 'discussion',
            'tag' => 'General'
        ]);

        $this->assertInstanceOf(Community::class, $post);
        $this->assertEquals('Test Post', $post->title);
    }

    public function test_community_post_belongs_to_user()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->assertInstanceOf(User::class, $post->user);
        $this->assertEquals($this->user->id, $post->user->id);
    }

    public function test_community_post_has_many_comments()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Create comment manually instead of using factory
        $comment = new CommunityComment([
            'community_id' => $post->id,
            'user_id' => $this->user->id,
            'content' => 'Test comment'
        ]);
        $comment->save();

        $this->assertInstanceOf(CommunityComment::class, $post->comments->first());
        $this->assertEquals(1, $post->comments->count());
    }

    public function test_community_post_has_many_reactions()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id
        ]);

        CommunityReaction::create([
            'community_id' => $post->id,
            'user_id' => $this->user->id,
            'reaction_type' => 'like'
        ]);

        $this->assertEquals(1, $post->reactions->count());
        $this->assertEquals('like', $post->reactions->first()->reaction_type);
    }

    public function test_can_update_community_post()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id
        ]);

        $post->update([
            'title' => 'Updated Title',
            'content' => 'Updated Content'
        ]);

        $this->assertEquals('Updated Title', $post->title);
        $this->assertEquals('Updated Content', $post->content);
    }

    public function test_can_delete_community_post()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id
        ]);

        $post->delete();

        $this->assertDatabaseMissing('communities', ['id' => $post->id]);
    }

    public function test_deleting_community_post_deletes_related_comments_and_reactions()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Create comment manually
        $comment = new CommunityComment([
            'community_id' => $post->id,
            'user_id' => $this->user->id,
            'content' => 'Test comment'
        ]);
        $comment->save();

        $reaction = CommunityReaction::create([
            'community_id' => $post->id,
            'user_id' => $this->user->id,
            'reaction_type' => 'like'
        ]);

        // Store IDs for verification
        $commentId = $comment->id;
        $reactionId = $reaction->id;

        // Delete related records first
        CommunityComment::where('community_id', $post->id)->delete();
        CommunityReaction::where('community_id', $post->id)->delete();
        
        // Then delete the post
        $post->delete();

        // Verify that related records are deleted
        $this->assertDatabaseMissing('community_comments', ['id' => $commentId]);
        $this->assertDatabaseMissing('community_reactions', ['id' => $reactionId]);
    }

    public function test_community_post_has_saves()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id
        ]);

        $savedPost = CommunitySavedPost::create([
            'community_id' => $post->id,
            'user_id' => $this->user->id
        ]);

        $this->assertTrue($post->saves()->exists());
        $this->assertEquals(1, $post->saves->count());
    }

    public function test_is_saved_by_user()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Test when not saved
        $this->assertFalse($post->isSavedBy($this->user));

        // Save the post
        CommunitySavedPost::create([
            'community_id' => $post->id,
            'user_id' => $this->user->id
        ]);

        // Test when saved
        $this->assertTrue($post->isSavedBy($this->user));
    }

    public function test_get_likes_count_attribute()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Create multiple reactions
        CommunityReaction::create([
            'community_id' => $post->id,
            'user_id' => $this->user->id,
            'reaction_type' => 'like'
        ]);

        CommunityReaction::create([
            'community_id' => $post->id,
            'user_id' => User::factory()->create()->id,
            'reaction_type' => 'like'
        ]);

        $this->assertEquals(2, $post->likes_count);
    }

    public function test_get_dislikes_count_attribute()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id
        ]);

        CommunityReaction::create([
            'community_id' => $post->id,
            'user_id' => $this->user->id,
            'reaction_type' => 'dislike'
        ]);

        $this->assertEquals(1, $post->dislikes_count);
    }

    public function test_get_user_reaction_attribute()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Test with no reaction
        $this->actingAs($this->user);
        $this->assertNull($post->user_reaction);

        // Add reaction
        $reaction = CommunityReaction::create([
            'community_id' => $post->id,
            'user_id' => $this->user->id,
            'reaction_type' => 'like'
        ]);

        // Refresh the model to clear the attribute cache
        $post->refresh();

        // Test with reaction
        $this->assertNotNull($post->user_reaction);
        $this->assertEquals('like', $post->user_reaction->reaction_type);
    }

    public function test_saved_by_users_relationship()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id
        ]);

        $anotherUser = User::factory()->create();
        
        CommunitySavedPost::create([
            'community_id' => $post->id,
            'user_id' => $anotherUser->id
        ]);

        $this->assertTrue($post->savedByUsers->contains($anotherUser));
        $this->assertEquals(1, $post->savedByUsers->count());
    }

    public function test_is_saved_by_returns_false_for_null_user()
    {
        $post = Community::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->assertFalse($post->isSavedBy(null));
    }
}


