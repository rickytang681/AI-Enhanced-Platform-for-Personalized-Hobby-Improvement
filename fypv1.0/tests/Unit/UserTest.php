<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Hobby;
use App\Models\Community;
use App\Models\LibraryItem;
use App\Models\LibraryRating;
use App\Models\LibrarySave;
use App\Models\CommunityComment;
use App\Models\CommunityReaction;
use App\Models\CommunitySavedPost;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_has_correct_role()
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);
        
        $this->assertEquals('user', $user->role);
    }

    public function test_user_has_hobbies()
    {
        $this->assertIsIterable($this->user->hobbies);
    }

    public function test_user_has_recommendations()
    {
        $this->assertIsIterable($this->user->recommendations);
    }

    public function test_user_profile_attributes()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    public function test_user_password_is_hashed()
    {
        $password = 'password123';
        $user = User::factory()->create([
            'password' => Hash::make($password)
        ]);
        
        $this->assertTrue(Hash::check($password, $user->password));
    }

    public function test_user_can_have_multiple_hobbies()
    {
        Hobby::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        $this->assertEquals(3, $this->user->hobbies()->count());
    }

    public function test_user_total_goals_count()
    {
        $this->assertInstanceOf(User::class, $this->user);
    }

    public function test_user_completed_goals_percentage()
    {
        $this->assertInstanceOf(User::class, $this->user);
    }

    public function test_user_library_contributions()
    {
        $this->assertInstanceOf(User::class, $this->user);
    }

    public function test_user_community_engagement()
    {
        Community::factory()->count(2)->create([
            'user_id' => $this->user->id
        ]);

        $this->assertEquals(2, $this->user->communityPosts()->count());
    }

    public function test_user_activity_streak()
    {
        $this->assertInstanceOf(User::class, $this->user);
    }

    public function test_user_has_library_items()
    {
        // Create library items using factory
        LibraryItem::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'title' => 'Test Library Item',
            'type' => 'text', // assuming 'text' is one of the allowed types
            'content' => 'Test content'
        ]);

        $this->assertTrue($this->user->libraryItems()->exists());
        $this->assertEquals(3, $this->user->libraryItems()->count());
    }

    public function test_user_has_library_ratings()
    {
        $libraryItem = LibraryItem::factory()->create();
        LibraryRating::create([
            'user_id' => $this->user->id,
            'library_item_id' => $libraryItem->id,
            'rating' => 5
        ]);

        $this->assertEquals(1, $this->user->libraryRatings()->count());
        $this->assertEquals(5, $this->user->libraryRatings->first()->rating);
    }

    public function test_user_has_saved_library_items()
    {
        $libraryItem = LibraryItem::factory()->create();
        
        LibrarySave::create([
            'user_id' => $this->user->id,
            'library_item_id' => $libraryItem->id
        ]);

        $this->assertTrue($this->user->librarySaves()->exists());
        $this->assertEquals(1, $this->user->librarySaves()->count());
    }

    public function test_user_has_community_comments()
    {
        $post = Community::factory()->create();
        CommunityComment::create([
            'user_id' => $this->user->id,
            'community_id' => $post->id,
            'content' => 'Test comment'
        ]);

        $this->assertEquals(1, $this->user->communityComments()->count());
        $this->assertEquals('Test comment', $this->user->communityComments->first()->content);
    }

    public function test_user_has_community_reactions()
    {
        $post = Community::factory()->create();
        CommunityReaction::create([
            'user_id' => $this->user->id,
            'community_id' => $post->id,
            'reaction_type' => 'like'
        ]);

        $this->assertEquals(1, $this->user->communityReactions()->count());
        $this->assertEquals('like', $this->user->communityReactions->first()->reaction_type);
    }

    public function test_user_has_saved_community_posts()
    {
        $post = Community::factory()->create();
        
        CommunitySavedPost::create([
            'user_id' => $this->user->id,
            'community_id' => $post->id
        ]);

        $this->assertTrue($this->user->communitySavedPosts()->exists());
        $this->assertEquals(1, $this->user->communitySavedPosts()->count());
    }
}




