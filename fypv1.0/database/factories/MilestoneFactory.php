<?php

namespace Database\Factories;

use App\Models\Milestone;
use Illuminate\Database\Eloquent\Factories\Factory;

class MilestoneFactory extends Factory
{
    protected $model = Milestone::class;

    public function definition()
    {
        return [
            'goal_id' => \App\Models\Goal::factory(),
            'description' => $this->faker->sentence,
            'due_date' => $this->faker->dateTimeBetween('now', '+6 months'),
            'completed' => false
        ];
    }
}