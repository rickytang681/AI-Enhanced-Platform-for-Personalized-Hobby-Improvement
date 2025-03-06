<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Community;
use App\Models\Goal;
use App\Models\Milestone;
use App\Policies\CommunityPolicy;
use App\Policies\GoalPolicy;
use App\Policies\MilestonePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Community::class => CommunityPolicy::class,
        Goal::class => GoalPolicy::class,
        Milestone::class => MilestonePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define gates if needed
        Gate::define('manage-goal', function ($user, $goal) {
            return $user->id === $goal->user_id;
        });

        Gate::define('manage-milestone', function ($user, $milestone) {
            return $user->id === $milestone->goal->user_id;
        });
    }
}
