<?php

namespace Database\Factories;

use App\Models\LibraryRating;
use App\Models\User;
use App\Models\LibraryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class LibraryRatingFactory extends Factory
{
    protected $model = LibraryRating::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'library_item_id' => LibraryItem::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}