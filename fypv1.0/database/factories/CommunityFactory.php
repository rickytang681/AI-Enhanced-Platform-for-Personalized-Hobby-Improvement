<?php

namespace Database\Factories;

use App\Models\Community;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommunityFactory extends Factory
{
    protected $model = Community::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'post_type' => $this->faker->randomElement(['discussion', 'question', 'experience']),
            'tag' => $this->faker->randomElement(['General', 'Help', 'Discussion']),
            'cover_image' => null
        ];
    }
}


