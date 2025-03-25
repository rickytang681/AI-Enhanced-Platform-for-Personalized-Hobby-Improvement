<?php

namespace Database\Factories;

use App\Models\Hobby;
use Illuminate\Database\Eloquent\Factories\Factory;

class HobbyFactory extends Factory
{
    protected $model = Hobby::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'experience_level' => $this->faker->randomElement(['Beginner', 'Intermediate', 'Expert']),
            'user_id' => \App\Models\User::factory()
        ];
    }
}