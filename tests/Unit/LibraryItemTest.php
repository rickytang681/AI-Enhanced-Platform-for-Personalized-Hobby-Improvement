<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\LibraryItem;
use App\Models\User;
use App\Models\LibraryRating;

class LibraryItemTest extends TestCase
{
    public function test_average_rating_calculation()
    {
        $libraryItem = LibraryItem::factory()->create();
        
        LibraryRating::factory()->create([
            'library_item_id' => $libraryItem->id,
            'rating' => 4
        ]);
        LibraryRating::factory()->create([
            'library_item_id' => $libraryItem->id,
            'rating' => 5
        ]);

        $this->assertEquals(4.5, $libraryItem->getAverageRatingAttribute());
    }

    public function test_user_can_save_library_item()
    {
        $user = User::factory()->create();
        $libraryItem = LibraryItem::factory()->create();

        $this->assertFalse($libraryItem->isSavedBy($user));
        
        $libraryItem->saves()->create(['user_id' => $user->id]);
        
        $this->assertTrue($libraryItem->fresh()->isSavedBy($user));
    }
}