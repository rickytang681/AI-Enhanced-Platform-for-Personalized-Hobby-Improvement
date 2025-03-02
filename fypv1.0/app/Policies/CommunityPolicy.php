<?php

namespace App\Policies;

use App\Models\Community;
use App\Models\User;

class CommunityPolicy
{
    public function update(User $user, Community $post)
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Community $post)
    {
        return $user->id === $post->user_id;
    }
} 