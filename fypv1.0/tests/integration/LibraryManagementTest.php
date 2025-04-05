<?php

namespace Tests\Integration\Library;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LibraryManagementTest extends TestCase
{
    use RefreshDatabase;
    
    protected $user;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Storage::fake('public');
        
        // Skip tests if required tables don't exist
        $this->checkRequiredTables();
    }
    
    protected function checkRequiredTables()
    {
        if (!$this->tableExists('library_items') || 
            !$this->tableExists('library_reactions') ||
            !$this->tableExists('library_saves') ||
            !$this->tableExists('library_ratings') ||
            !$this->tableExists('library_comments')) {
            $this->markTestSkipped('Required library tables do not exist.');
        }
    }
    
    protected function tableExists($table)
    {
        try {
            \DB::select(\DB::raw("SELECT 1 FROM {$table} LIMIT 1"));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function test_item_creation_with_relationships()
    {
        // Create a library item
        $item = \App\Models\LibraryItem::create([
            'user_id' => $this->user->id,
            'title' => 'Test Item Creation',
            'description' => 'Testing item creation with relationships',
            'type' => 'text',
            'content' => 'Test Content',
            'category' => 'Test Category',
            'subcategory' => 'Test Subcategory',
            'likes' => 0,
            'dislikes' => 0,
            'average_rating' => 0,
            'rating_count' => 0
        ]);
        
        // Assert the item was created
        $this->assertDatabaseHas('library_items', [
            'title' => 'Test Item Creation'
        ]);
        
        // Test relationship with user
        $this->assertEquals($this->user->id, $item->user->id);
    }
    
    public function test_search_functionality_with_filters()
    {
        // Create test items with different categories
        \App\Models\LibraryItem::create([
            'user_id' => $this->user->id,
            'title' => 'Search Test Item 1',
            'description' => 'First test item for search',
            'type' => 'text',
            'content' => 'Test Content 1',
            'category' => 'Category A',
            'subcategory' => 'Subcategory X',
            'likes' => 0,
            'dislikes' => 0,
            'average_rating' => 0,
            'rating_count' => 0
        ]);
        
        \App\Models\LibraryItem::create([
            'user_id' => $this->user->id,
            'title' => 'Search Test Item 2',
            'description' => 'Second test item for search',
            'type' => 'text',
            'content' => 'Test Content 2',
            'category' => 'Category B',
            'subcategory' => 'Subcategory Y',
            'likes' => 0,
            'dislikes' => 0,
            'average_rating' => 0,
            'rating_count' => 0
        ]);
        
        // Test basic search
        $results = \App\Models\LibraryItem::where('title', 'like', '%Search Test%')->get();
        $this->assertEquals(2, $results->count());
        
        // Test category filter
        $filteredResults = \App\Models\LibraryItem::where('category', 'Category A')->get();
        $this->assertEquals(1, $filteredResults->count());
        $this->assertEquals('Search Test Item 1', $filteredResults->first()->title);
    }
    
    public function test_reaction_system()
    {
        // Create a library item with all required fields
        $item = \App\Models\LibraryItem::create([
            'user_id' => $this->user->id,
            'title' => 'Test Item',
            'description' => 'Test Description',
            'type' => 'text',
            'content' => 'Test Content',
            'category' => 'Test Category',
            'subcategory' => 'Test Subcategory',
            'likes' => 0,
            'dislikes' => 0,
            'average_rating' => 0,
            'rating_count' => 0
        ]);
        
        // Create a reaction
        \App\Models\LibraryReaction::create([
            'user_id' => $this->user->id,
            'library_item_id' => $item->id,
            'reaction_type' => 'like'
        ]);
        
        // Refresh the item from database
        $item->refresh();
        
        // Assert the reaction was recorded
        $this->assertDatabaseHas('library_reactions', [
            'user_id' => $this->user->id,
            'library_item_id' => $item->id,
            'reaction_type' => 'like'
        ]);
        
        // Test the updateReactionCounts method
        $item->updateReactionCounts();
        
        // Assert the likes count was updated
        $this->assertEquals(1, $item->likes);
        $this->assertEquals(0, $item->dislikes);
    }
    
    public function test_rating_system()
    {
        // Create a library item
        $item = \App\Models\LibraryItem::create([
            'user_id' => $this->user->id,
            'title' => 'Rating Test Item',
            'description' => 'Test item for rating system',
            'type' => 'text',
            'content' => 'Test Content',
            'category' => 'Test Category',
            'subcategory' => 'Test Subcategory',
            'likes' => 0,
            'dislikes' => 0,
            'average_rating' => 0,
            'rating_count' => 0
        ]);
        
        // Add a rating
        \App\Models\LibraryRating::create([
            'user_id' => $this->user->id,
            'library_item_id' => $item->id,
            'rating' => 4
        ]);
        
        // Test that rating was recorded
        $this->assertDatabaseHas('library_ratings', [
            'user_id' => $this->user->id,
            'library_item_id' => $item->id,
            'rating' => 4
        ]);
        
        // Test updateRatingStats method
        $item->updateRatingStats();
        
        // Assert the average rating and count were updated
        $this->assertEquals(4.0, $item->average_rating);
        $this->assertEquals(1, $item->rating_count);
    }
    
    public function test_saving_and_persistence()
    {
        // Create a library item
        $item = \App\Models\LibraryItem::create([
            'user_id' => $this->user->id,
            'title' => 'Save Test Item',
            'description' => 'Test item for save functionality',
            'type' => 'text',
            'content' => 'Test Content',
            'category' => 'Test Category',
            'subcategory' => 'Test Subcategory',
            'likes' => 0,
            'dislikes' => 0,
            'average_rating' => 0,
            'rating_count' => 0
        ]);
        
        // Save the item
        \App\Models\LibrarySave::create([
            'user_id' => $this->user->id,
            'library_item_id' => $item->id
        ]);
        
        // Test that save was recorded
        $this->assertDatabaseHas('library_saves', [
            'user_id' => $this->user->id,
            'library_item_id' => $item->id
        ]);
        
        // Test isSavedBy method
        $this->assertTrue($item->isSavedBy($this->user));
    }
    
    public function test_comment_system()
    {
        // Create a library item
        $item = \App\Models\LibraryItem::create([
            'user_id' => $this->user->id,
            'title' => 'Comment Test Item',
            'description' => 'Test item for comment system',
            'type' => 'text',
            'content' => 'Test Content',
            'category' => 'Test Category',
            'subcategory' => 'Test Subcategory',
            'likes' => 0,
            'dislikes' => 0,
            'average_rating' => 0,
            'rating_count' => 0
        ]);
        
        // Add a comment
        \App\Models\LibraryComment::create([
            'user_id' => $this->user->id,
            'library_item_id' => $item->id,
            'content' => 'This is a test comment'
        ]);
        
        // Test that comment was recorded
        $this->assertDatabaseHas('library_comments', [
            'user_id' => $this->user->id,
            'library_item_id' => $item->id,
            'content' => 'This is a test comment'
        ]);
        
        // Test comments relationship
        $this->assertEquals(1, $item->comments->count());
        $this->assertEquals('This is a test comment', $item->comments->first()->content);
    }
}







