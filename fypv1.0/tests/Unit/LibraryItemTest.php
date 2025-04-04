<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\LibraryItem;
use App\Models\LibraryComment;
use App\Models\LibraryReaction;
use App\Models\LibraryRating;
use App\Models\LibrarySave;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LibraryItemTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $libraryItem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->libraryItem = LibraryItem::factory()->create([
            'user_id' => $this->user->id
        ]);
    }

    public function test_library_item_has_many_comments()
    {
        $item = LibraryItem::factory()->create([
            'user_id' => $this->user->id
        ]);

        LibraryComment::factory()->create([
            'library_item_id' => $item->id,
            'user_id' => $this->user->id,
            'content' => 'Test comment'
        ]);

        $this->assertEquals(1, $item->comments->count());
        $this->assertEquals('Test comment', $item->comments->first()->content);
    }

    public function test_deleting_library_item_deletes_related_comments_and_reactions()
    {
        $item = LibraryItem::factory()->create([
            'user_id' => $this->user->id
        ]);

        $comment = LibraryComment::factory()->create([
            'library_item_id' => $item->id,
            'user_id' => $this->user->id
        ]);

        $reaction = LibraryReaction::factory()->create([
            'library_item_id' => $item->id,
            'user_id' => $this->user->id
        ]);

        $item->delete();

        $this->assertDatabaseMissing('library_comments', ['id' => $comment->id]);
        $this->assertDatabaseMissing('library_reactions', ['id' => $reaction->id]);
    }

    public function test_library_item_has_ratings()
    {
        $rating = LibraryRating::create([
            'library_item_id' => $this->libraryItem->id,
            'user_id' => $this->user->id,
            'rating' => 4
        ]);

        $this->assertTrue($this->libraryItem->ratings()->exists());
        $this->assertEquals(4, $this->libraryItem->ratings->first()->rating);
    }

    public function test_library_item_has_saves()
    {
        $save = LibrarySave::create([
            'library_item_id' => $this->libraryItem->id,
            'user_id' => $this->user->id
        ]);

        $this->assertTrue($this->libraryItem->saves()->exists());
    }

    public function test_is_saved_by_user()
    {
        // Test when not saved
        $this->assertFalse($this->libraryItem->isSavedBy($this->user));

        // Save the item
        LibrarySave::create([
            'library_item_id' => $this->libraryItem->id,
            'user_id' => $this->user->id
        ]);

        // Test when saved
        $this->assertTrue($this->libraryItem->isSavedBy($this->user));
    }

    public function test_get_user_rating()
    {
        // Test with no rating
        $this->assertNull($this->libraryItem->userRating($this->user));

        // Add rating
        $rating = LibraryRating::create([
            'library_item_id' => $this->libraryItem->id,
            'user_id' => $this->user->id,
            'rating' => 5
        ]);

        // Test with rating
        $userRating = $this->libraryItem->userRating($this->user);
        $this->assertNotNull($userRating);
        $this->assertEquals(5, $userRating->rating);
    }

    public function test_get_user_reaction()
    {
        // Test with no reaction
        $this->assertNull($this->libraryItem->userReaction());

        // Add reaction
        $reaction = LibraryReaction::create([
            'library_item_id' => $this->libraryItem->id,
            'user_id' => $this->user->id,
            'reaction_type' => 'like'
        ]);

        // Test with reaction
        $this->actingAs($this->user);
        $userReaction = $this->libraryItem->userReaction();
        $this->assertNotNull($userReaction);
        $this->assertEquals('like', $userReaction->reaction_type);
    }

    public function test_update_reaction_counts()
    {
        // Create multiple reactions
        LibraryReaction::create([
            'library_item_id' => $this->libraryItem->id,
            'user_id' => $this->user->id,
            'reaction_type' => 'like'
        ]);

        LibraryReaction::create([
            'library_item_id' => $this->libraryItem->id,
            'user_id' => User::factory()->create()->id,
            'reaction_type' => 'like'
        ]);

        LibraryReaction::create([
            'library_item_id' => $this->libraryItem->id,
            'user_id' => User::factory()->create()->id,
            'reaction_type' => 'dislike'
        ]);

        $this->libraryItem->updateReactionCounts();

        $this->assertEquals(2, $this->libraryItem->likes);
        $this->assertEquals(1, $this->libraryItem->dislikes);
    }

    public function test_update_rating_stats()
    {
        // Create multiple ratings
        LibraryRating::create([
            'library_item_id' => $this->libraryItem->id,
            'user_id' => $this->user->id,
            'rating' => 5
        ]);

        LibraryRating::create([
            'library_item_id' => $this->libraryItem->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 3
        ]);

        $this->libraryItem->updateRatingStats();

        $this->assertEquals(4.0, $this->libraryItem->average_rating);
        $this->assertEquals(2, $this->libraryItem->rating_count);
    }

    public function test_get_file_url()
    {
        // Test with no file
        $this->assertNull($this->libraryItem->getFileUrl());

        // Test with file
        $this->libraryItem->file_path = 'test/path/file.pdf';
        $this->libraryItem->save();

        Storage::fake('public');
        $expectedUrl = Storage::disk('public')->url('test/path/file.pdf');
        $this->assertEquals($expectedUrl, $this->libraryItem->getFileUrl());
    }

    public function test_scope_by_category()
    {
        LibraryItem::factory()->create([
            'category' => 'TestCategory'
        ]);

        $items = LibraryItem::byCategory('TestCategory')->get();
        $this->assertCount(1, $items);
        $this->assertEquals('TestCategory', $items->first()->category);
    }

    public function test_deleting_library_item_deletes_related_ratings()
    {
        $rating = LibraryRating::create([
            'library_item_id' => $this->libraryItem->id,
            'user_id' => $this->user->id,
            'rating' => 5
        ]);

        $this->libraryItem->delete();

        $this->assertDatabaseMissing('library_ratings', ['id' => $rating->id]);
    }

    public function test_deleting_library_item_deletes_related_saves()
    {
        $save = LibrarySave::create([
            'library_item_id' => $this->libraryItem->id,
            'user_id' => $this->user->id
        ]);

        $this->libraryItem->delete();

        $this->assertDatabaseMissing('library_saves', ['id' => $save->id]);
    }

    public function test_library_item_belongs_to_user()
    {
        $this->assertInstanceOf(User::class, $this->libraryItem->user);
        $this->assertEquals($this->user->id, $this->libraryItem->user->id);
    }

    public function test_library_item_type_validation()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        LibraryItem::create([
            'user_id' => $this->user->id,
            'title' => 'Test Item',
            'type' => 'invalid-type'
        ]);
    }

    public function test_library_item_category_validation()
    {
        $item = LibraryItem::factory()->create([
            'user_id' => $this->user->id,
            'category' => 'TestCategory'
        ]);

        $this->assertEquals('TestCategory', $item->category);
    }

    public function test_average_rating_calculation()
    {
        LibraryRating::create([
            'library_item_id' => $this->libraryItem->id,
            'user_id' => $this->user->id,
            'rating' => 4
        ]);

        LibraryRating::create([
            'library_item_id' => $this->libraryItem->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 2
        ]);

        $this->libraryItem->updateRatingStats();
        $this->assertEquals(3.0, $this->libraryItem->average_rating);
    }

    public function test_library_item_has_valid_file_type()
    {
        $item = LibraryItem::factory()->create([
            'user_id' => $this->user->id,
            'file_path' => 'test/file.pdf'
        ]);

        $this->assertTrue(in_array(
            pathinfo($item->file_path, PATHINFO_EXTENSION),
            ['pdf', 'doc', 'docx', 'txt']
        ));
    }

    public function test_library_item_search_by_title()
    {
        LibraryItem::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Unique Search Title'
        ]);

        $results = LibraryItem::where('title', 'like', '%Search%')->get();
        $this->assertCount(1, $results);
        $this->assertEquals('Unique Search Title', $results->first()->title);
    }

    public function test_library_item_with_multiple_ratings()
    {
        // Create multiple ratings with different values
        $ratings = [5, 3, 4];
        foreach ($ratings as $rating) {
            LibraryRating::create([
                'library_item_id' => $this->libraryItem->id,
                'user_id' => User::factory()->create()->id,
                'rating' => $rating
            ]);
        }

        $this->libraryItem->updateRatingStats();
        $this->assertEquals(4.0, $this->libraryItem->average_rating);
        $this->assertEquals(3, $this->libraryItem->rating_count);
        $this->assertEquals(min($ratings), $this->libraryItem->ratings()->min('rating'));
        $this->assertEquals(max($ratings), $this->libraryItem->ratings()->max('rating'));
    }

    public function test_library_item_content_required_for_text_type()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        LibraryItem::create([
            'user_id' => $this->user->id,
            'title' => 'Test Item',
            'type' => 'text',
            'category' => 'Test Category',
            // Missing content field
        ]);
    }

    public function test_library_item_update_timestamps()
    {
        $originalUpdatedAt = $this->libraryItem->updated_at;

        sleep(1); // Wait 1 second to ensure timestamp difference
        
        $this->libraryItem->update([
            'title' => 'Updated Title'
        ]);

        $this->libraryItem->refresh();
        $this->assertTrue($this->libraryItem->updated_at->gt($originalUpdatedAt));
    }
}

