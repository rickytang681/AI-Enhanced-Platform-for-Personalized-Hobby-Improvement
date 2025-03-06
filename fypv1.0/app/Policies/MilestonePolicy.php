<?php

namespace App\Policies;

use App\Models\Milestone;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MilestonePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Milestone $milestone)
    {
        return $user->id === $milestone->goal->user_id;
    }

    public function update(User $user, Milestone $milestone)
    {
        return $user->id === $milestone->goal->user_id;
    }

    public function delete(User $user, Milestone $milestone)
    {
        return $user->id === $milestone->goal->user_id;
    }

    public function manage(User $user, Milestone $milestone)
    {
        return $user->id === $milestone->goal->user_id;
    }
}
