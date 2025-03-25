<?php

namespace Database\Factories;

use App\Models\LibraryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class LibraryItemFactory extends Factory
{
    protected $model = LibraryItem::class;

    public function definition()
    {
        $type = $this->faker->randomElement(['text', 'video']);
        
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'type' => $type,
            'content' => $type === 'text' ? $this->faker->paragraphs(3, true) : null,
            'video_url' => $type === 'video' ? 'https://www.youtube.com/embed/' . $this->faker->lexify('????????????') : null,
            'category' => $this->faker->word,
            'subcategory' => $this->faker->word,
            'likes' => 0,
            'dislikes' => 0,
            'average_rating' => 0,
            'rating_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
