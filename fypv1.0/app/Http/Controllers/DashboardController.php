<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hobby;
use App\Models\Goal;
use App\Models\Community;
use App\Models\LibraryItem;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        
        // Get user's hobbies with their goals
        $hobbies = $user->hobbies()
            ->withCount(['goals', 'goals as completed_goals_count' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->get();

        // Get recent community posts
        $recentCommunityPosts = Community::latest()
            ->take(3)
            ->get();

        // Get recent library resources (changed from popular to recent)
        $popularResources = LibraryItem::latest()
            ->take(3)
            ->get();

        // Calculate overall progress
        $totalGoals = $hobbies->sum('goals_count');
        $completedGoals = $hobbies->sum('completed_goals_count');
        $overallProgress = $totalGoals > 0 
            ? round(($completedGoals / $totalGoals) * 100) 
            : 0;

        return view('dashboard', compact(
            'hobbies',
            'recentCommunityPosts',
            'popularResources',
            'overallProgress'
        ));
    }
} 
