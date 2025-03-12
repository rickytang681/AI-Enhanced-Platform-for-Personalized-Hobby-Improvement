<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hobby;
use App\Models\Recommendation;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class RecommendationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $hobbies = $user->hobbies()->with(['goals'])->get();

        // Redirect to hobbies page if no hobbies exist
        if ($hobbies->isEmpty()) {
            return redirect()->route('hobbies.index')
                ->with('warning', 'Please add some hobbies first to get personalized recommendations.');
        }

        $recommendations = $user->recommendations()->latest()->get();
        return view('recommendation', compact('hobbies', 'recommendations'));
    }

    public function getRecommendations(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Validate request
            $validated = $request->validate([
                'selected_hobbies' => 'required|array|size:1',
                'selected_hobbies.*' => 'exists:hobbies,id',
                'selected_goals' => 'required|array|size:1',
                'selected_goals.*' => 'exists:goals,id'
            ]);

            // Get selected hobbies with their goals and milestones
            $hobbies = $user->hobbies()
                ->whereIn('id', $validated['selected_hobbies'])
                ->with(['goals' => function($query) use ($validated) {
                    if (!empty($validated['selected_goals'])) {
                        $query->whereIn('id', $validated['selected_goals']);
                    }
                    $query->with('milestones');
                }])
                ->get();

            if ($hobbies->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Please add some hobbies and goals first to get personalized recommendations.'
                ]);
            }

            // Prepare the context for AI
            $context = "User's selected hobbies and goals:\n\n";
            foreach ($hobbies as $hobby) {
                $context .= "Hobby: {$hobby->name} (Level: {$hobby->experience_level})\n";
                if ($hobby->goals->isNotEmpty()) {
                    $context .= "Goals:\n";
                    foreach ($hobby->goals as $goal) {
                        $context .= "- Goal: {$goal->title} (Status: {$goal->status})\n";
                        
                        // Add milestones information
                        if ($goal->milestones->isNotEmpty()) {
                            $context .= "  Milestones:\n";
                            foreach ($goal->milestones as $milestone) {
                                $context .= "  * {$milestone->description} " . 
                                          "(Due: {$milestone->due_date}, " .
                                          "Status: " . ($milestone->completed ? "Completed" : "Pending") . ")\n";
                            }
                        }
                    }
                    $context .= "\n";
                }
            }

            $prompt = "You are a professional hobby improvement coach. Based on the user's selected hobbies, goals, and milestones, provide personalized, actionable recommendations for improvement. Follow these guidelines:\n\n" .
                     "1. **Context**:\n" . $context . "\n\n" .
                     "2. **Recommendation Requirements**:\n" .
                     "- Each recommendation should be specific, actionable, and tailored to the user's current progress.\n" .
                     "- Include the following details for each recommendation:\n" .
                     "  * **Title**: A short, descriptive title.\n" .
                     "  * **Action Steps**: Clear steps to achieve the recommendation.\n" .
                     "  * **Time Commitment**: Estimated time required (e.g., 30 minutes/day).\n" .
                     "  * **Resources**: Suggested tools, books, or online resources (if applicable).\n" .
                     "  * **Expected Outcome**: What the user can expect to achieve.\n\n" .
                     "3. **Focus Areas**:\n" .
                     "- **Progress on Incomplete Milestones**: Provide steps to complete pending milestones.\n" .
                     "- **New Milestones**: Suggest new milestones if the current ones are too easy or outdated.\n" .
                     "- **Motivation & Consistency**: Offer tips to stay motivated and consistent.\n" .
                     "- **Skill Development**: Recommend techniques or resources to improve specific skills.\n\n" .
                     "4. **Tone & Style**:\n" .
                     "- Use a friendly and encouraging tone.\n" .
                     "- Keep the language simple and easy to understand.\n\n" .
                     "5. **Output Format**:\n" .
                     "- Provide the recommendations in a numbered list.\n" .
                     "- Each recommendation should follow the structure above.\n\n" .
                     "Now, generate the recommendations based on the provided context. At The first need showing the hobby, goals, milestone, and exexperience_level of user. And remove ** and bolding the important sentence";

            $client = new Client([
                'verify' => false,
                'timeout' => 30
            ]);

            $response = $client->request('POST', env('RAPIDAPI_URL'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-RapidAPI-Key' => env('RAPIDAPI_KEY'),
                    'X-RapidAPI-Host' => env('RAPIDAPI_HOST')
                ],
                'json' => [
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'web_access' => false,
                    'system_prompt' => '',
                    'temperature' => 0.8,
                    'top_k' => 10,
                    'top_p' => 0.9,
                    'max_tokens' => 1000
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            // Log the response for debugging
            \Log::info('API Response:', ['result' => $result]);

            if (isset($result['result']) && $result['status'] === true) {
                // Save the raw recommendation content
                $recommendation = Recommendation::create([
                    'user_id' => $user->id,
                    'content' => $result['result']
                ]);

                return response()->json([
                    'success' => true,
                    'recommendations' => $result['result'],
                    'recommendation_id' => $recommendation->id
                ]);
            } else {
                \Log::error('API Response Error:', ['result' => $result]);
                throw new \Exception('API returned an unsuccessful response: ' . json_encode($result));
            }

        } catch (GuzzleException $e) {
            \Log::error('Guzzle Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Network error while getting recommendations. Please try again later.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Recommendation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to get recommendations. Please try again later. Error: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $recommendation = Recommendation::where('user_id', auth()->id())
                                          ->where('id', $id)
                                          ->firstOrFail();
            $recommendation->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete recommendation.'
            ]);
        }
    }
}
