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

    public function index(Request $request)
    {
        $user = auth()->user();
        $hobbies = $user->hobbies()->with(['goals'])->get();

        // Redirect to hobbies page if no hobbies exist
        if ($hobbies->isEmpty()) {
            return redirect()->route('hobbies.index')
                ->with('warning', 'Please add some hobbies first to get personalized recommendations.');
        }

        $recommendations = $user->recommendations()->latest()->get();

        // Get selected hobby and goal from the request
        $selectedHobbyId = $request->query('hobby_id');
        $selectedGoalId = $request->query('goal_id');

        // If both hobby and goal are selected, automatically generate recommendation
        $autoGenerate = $selectedHobbyId && $selectedGoalId;

        return view('recommendation', compact('hobbies', 'recommendations', 'selectedHobbyId', 'selectedGoalId', 'autoGenerate'));
    }

    public function getRecommendations(Request $request)
    {
        $validator = validator($request->all(), [
            'selected_hobbies' => 'required|array|min:1',
            'selected_hobbies.*' => 'exists:hobbies,id',
            'selected_goals' => 'required|array|min:1',
            'selected_goals.*' => 'exists:goals,id'
        ], [
            'selected_hobbies.required' => 'Please select at least one hobby.',
            'selected_hobbies.min' => 'Please select at least one hobby.',
            'selected_goals.required' => 'Please select at least one goal.',
            'selected_goals.min' => 'Please select at least one goal.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = auth()->user();
            $validated = $validator->validated();

            // Check if the selected hobbies belong to the authenticated user
            $userHobbies = $user->hobbies()->whereIn('id', $validated['selected_hobbies'])->get();
            if ($userHobbies->count() !== count($validated['selected_hobbies'])) {
                return response()->json(['error' => 'Unauthorized access to hobbies'], 403);
            }

            // Get selected hobbies with their goals and milestones
            $hobbies = $user->hobbies()
                ->whereIn('id', $validated['selected_hobbies'])
                ->with(['goals' => function($query) use ($validated) {
                    $query->whereIn('id', $validated['selected_goals'])  // This ensures only the selected goal is included
                          ->with('milestones');
                }])
                ->get();

            if ($hobbies->isEmpty()) {
                \Log::warning('No hobbies found for user', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'error' => 'No hobbies found. Please add some hobbies first.'
                ]);
            }

            // Prepare the context for AI
            $context = "User's selected hobbies and goals:\n\n";
            foreach ($hobbies as $hobby) {
                $context .= "Hobby: {$hobby->name} (Level: {$hobby->experience_level})\n";
                if ($hobby->goals->isNotEmpty()) {
                    $context .= "Goals:\n";
                    foreach ($hobby->goals as $goal) {
                        // Changed from $goal->title to $goal->goal
                        $context .= "- Goal: {$goal->goal} (Status: {$goal->status})\n";
                        
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

            // Log the context for debugging
            \Log::info('AI Context:', ['context' => $context]);

            $prompt = "
            You are a professional hobby improvement coach. Based on the user's selected hobbies, goals, and milestones, provide personalized, actionable recommendations for improvement. Follow these guidelines:
            
            1. Context:  
               - Display the user's hobby, goal, milestones, and experience level at the beginning.  
               - Example format:  
                 $context
            
            2. Recommendation Requirements:  
               - Each recommendation must be specific, actionable, and tailored to the user's current progress.  
               - Include the following details for each recommendation:  
                 - Title: A short, descriptive title.  
                 - Action Steps: Clear, step-by-step instructions to achieve the recommendation.  
                 - Time Commitment: Estimated time required (e.g., 30 minutes/day, 2 hours/week).  
                 - Resources: Suggested tools, books, online courses, or other resources (if applicable).  
                 - Expected Outcome: What the user can expect to achieve by following the recommendation.  
            
            3. Focus Areas:  
               - Progress on Incomplete Milestones: Provide actionable steps to help the user complete pending milestones.  
               - New Milestones: Suggest new milestones if the current ones are too easy, outdated, or already completed.  
               - Motivation & Consistency: Offer practical tips to help the user stay motivated and consistent in their practice.  
               - Skill Development: Recommend techniques, exercises, or resources to improve specific skills related to the hobby.  
            
            4. Tone & Style:  
               - Use a friendly, encouraging, and professional tone.  
               - Keep the language simple, clear, and easy to understand.  
               - Avoid jargon unless necessary, and explain any technical terms.  
            
            5. Output Format:  
               - Start by displaying the user's hobby, goal, milestones, and experience level in a clear format.  
               - Provide recommendations in a numbered list.  
               - Each recommendation should follow this structure:  
                 1. Title: [Title of the recommendation]  
                    - Action Steps: [Step-by-step instructions]  
                    - Time Commitment: [Estimated time]  
                    - Resources: [Suggested resources]  
                    - Expected Outcome: [What the user will achieve]  
            
            6. Additional Notes:  
               - If the user's milestones are too vague, suggest more specific and measurable milestones.  
               - If the user is struggling with motivation, include tips on setting small, achievable goals and tracking progress.  
               - If the user is advanced, focus on refining skills, exploring new techniques, or tackling challenging projects.  
            
            7. Formatting:
               - Use proper HTML formatting for structure.
               - Wrap titles in <h3> tags.
               - Use <p> tags for paragraphs.
               - Use <ul> and <li> tags for lists.
               - Use <br> for line breaks where needed.
               - Use <strong> or <b> tags for emphasis.
               - Ensure proper spacing between sections with appropriate HTML elements.

            Now, generate the recommendations based on the provided context. Ensure the output is clear, actionable, and tailored to the user's needs.";

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

            // Log the API response for debugging
            \Log::info('API Response:', ['result' => $result]);

            if (isset($result['result']) && $result['status'] === true) {
                // Process the content to ensure proper HTML formatting
                $content = $result['result'];
                
                // Clean up any existing HTML tags first
                $content = strip_tags($content);
                
                // Replace any markdown bold syntax with HTML bold tags
                $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);
                
                // Format titles that start with "Title:" or numbered titles
                $content = preg_replace('/(^|\n)(Title:|[\d]+\.\s+)([^\n]+)/', '$1<h3>$3</h3>', $content);
                
                // Format sections with specific labels
                $sections = ['Action Steps:', 'Time Commitment:', 'Resources:', 'Expected Outcome:'];
                foreach ($sections as $section) {
                    $content = preg_replace('/(\n|\r\n)' . preg_quote($section) . '(\s*)/', '<strong>' . $section . '</strong><br>', $content);
                }
                
                // Convert bullet points
                $content = preg_replace('/(\n|\r\n)\s*\*\s+([^\n\r]+)/', '<li>$2</li>', $content);
                $content = preg_replace('/(\<li\>.*?\<\/li\>)+/', '<ul>$0</ul>', $content);
                
                // Add paragraph tags to regular text blocks (lines that don't start with HTML)
                $content = preg_replace('/(\n|\r\n)([^<\n\r][^\n\r]+)(\n|\r\n|$)/', '$1<p>$2</p>$3', $content);
                
                // Wrap each recommendation in a div
                $pattern = '/<h3>(.+?)<\/h3>(.*?)(?=<h3>|$)/s';
                preg_match_all($pattern, $content, $matches);
                
                $formattedContent = '';
                if (!empty($matches[0])) {
                    foreach ($matches[0] as $match) {
                        $formattedContent .= '<div class="recommendation-item">' . $match . '</div>';
                    }
                } else {
                    // If no matches found, just wrap the whole content
                    $formattedContent = '<div class="recommendation-item">' . $content . '</div>';
                }
                
                // Save the recommendation
                $recommendation = Recommendation::create([
                    'user_id' => $user->id,
                    'hobby_id' => $validated['selected_hobbies'][0],
                    'goal_id' => $validated['selected_goals'][0],
                    'content' => $formattedContent
                ]);

                return response()->json([
                    'success' => true,
                    'recommendations' => $formattedContent,
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
            $recommendation = Recommendation::findOrFail($id);
            
            if ($recommendation->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            $recommendation->delete();
            return response()->json(['success' => true]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Recommendation not found'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete recommendation.'
            ], 500);
        }
    }
}
