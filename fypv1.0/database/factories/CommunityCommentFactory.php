<?php

namespace Database\Factories;

use App\Models\CommunityComment;
use App\Models\User;
use App\Models\Community;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommunityCommentFactory extends Factory
{
    protected $model = CommunityComment::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'community_id' => Community::factory(),
            'content' => $this->faker->paragraph(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}