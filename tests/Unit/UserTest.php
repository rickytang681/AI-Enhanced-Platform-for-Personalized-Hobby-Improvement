<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hobby;
use App\Models\LibraryItem;

class UserTest extends TestCase
{
    public function test_user_has_hobbies()
    {
        $user = User::factory()->create();
        $hobby = Hobby::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->hobbies->contains($hobby));
    }

    public function test_user_can_create_library_items()
    {
        $user = User::factory()->create();
        $libraryItem = LibraryItem::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Item',
            'type' => 'text'
        ]);

        $this->assertTrue($user->libraryItems()->exists());
        $this->assertEquals('Test Item', $libraryItem->title);
    }

    public function test_user_role_assignment()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->assertEquals('user', $user->role);
    }
}