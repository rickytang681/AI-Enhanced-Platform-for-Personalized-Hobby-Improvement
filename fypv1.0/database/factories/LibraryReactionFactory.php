<?php

namespace Database\Factories;

use App\Models\LibraryReaction;
use App\Models\User;
use App\Models\LibraryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class LibraryReactionFactory extends Factory
{
    protected $model = LibraryReaction::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'library_item_id' => LibraryItem::factory(),
            'reaction_type' => $this->faker->randomElement(['like', 'dislike']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}