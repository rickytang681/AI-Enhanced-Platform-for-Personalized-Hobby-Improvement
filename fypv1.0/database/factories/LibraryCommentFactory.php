<?php

namespace Database\Factories;

use App\Models\LibraryComment;
use App\Models\User;
use App\Models\LibraryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class LibraryCommentFactory extends Factory
{
    protected $model = LibraryComment::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'library_item_id' => LibraryItem::factory(),
            'content' => $this->faker->paragraph,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}