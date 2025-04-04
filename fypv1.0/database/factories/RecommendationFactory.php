<?php

namespace Database\Factories;

use App\Models\Recommendation;
use App\Models\User;
use App\Models\Hobby;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecommendationFactory extends Factory
{
    protected $model = Recommendation::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'hobby_id' => Hobby::factory(),
            'content' => $this->faker->paragraph,
            'type' => 'hobby',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
