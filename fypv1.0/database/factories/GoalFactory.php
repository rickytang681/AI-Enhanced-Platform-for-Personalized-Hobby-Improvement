<?php

namespace Database\Factories;

use App\Models\Goal;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoalFactory extends Factory
{
    protected $model = Goal::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'hobby_id' => \App\Models\Hobby::factory(),
            'goal' => $this->faker->sentence,
            'progress' => 0,
            'status' => 'in-progress',
            'deadline' => $this->faker->dateTimeBetween('+1 month', '+1 year'),
            'notes' => $this->faker->optional()->text
        ];
    }
}