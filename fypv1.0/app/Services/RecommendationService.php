<?php

namespace App\Services;

use App\Models\Recommendation;
use App\Models\User;
use App\Models\Hobby;
use App\Models\Goal;
use Illuminate\Support\Facades\Http;

class RecommendationService
{
    public function generateRecommendation(User $user, array $hobbies, array $goals)
    {
        // This is a placeholder for the actual recommendation generation logic
        // In a real application, this might call an external API or use ML models
        return [
            'status' => true,
            'result' => 'Based on your hobby and goals, we recommend focusing on structured learning and practice.'
        ];
    }

    public function createRecommendation(array $data)
    {
        return Recommendation::create($data);
    }

    public function deleteRecommendation($id)
    {
        $recommendation = Recommendation::findOrFail($id);
        return $recommendation->delete();
    }

    public function getUserRecommendations(User $user)
    {
        return Recommendation::where('user_id', $user->id)
                           ->with(['hobby', 'goal'])
                           ->latest()
                           ->get();
    }
}