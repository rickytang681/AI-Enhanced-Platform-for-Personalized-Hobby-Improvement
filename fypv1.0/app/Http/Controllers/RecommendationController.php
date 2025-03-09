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
            $hobbies = $user->hobbies()->with(['goals'])->get();

            if ($hobbies->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Please add some hobbies and goals first to get personalized recommendations.'
                ]);
            }

            // Prepare the context for AI
            $context = "User's hobbies and goals:\n";
            foreach ($hobbies as $hobby) {
                $context .= "Hobby: {$hobby->name}\n";
                $context .= "Goals:\n";
                foreach ($hobby->goals as $goal) {
                    $context .= "- {$goal->title} (Status: {$goal->status})\n";
                }
            }

            $prompt = "Based on these hobbies and goals, provide personalized recommendations for improvement and next steps. Please provide specific, actionable recommendations that will help the user progress in their hobbies and achieve their goals. Format the response in clear, numbered points:\n" . $context;

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
                // Save the recommendation
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
