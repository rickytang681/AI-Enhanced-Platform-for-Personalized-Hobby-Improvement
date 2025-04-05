<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Hobby;
use App\Models\Goal;
use App\Models\Milestone;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HobbySeeder extends Seeder
{
    public function run()
    {
        // Get all users
        $users = User::all();
        
        // List of hobbies with descriptions
        $hobbies = [
            'Photography' => 'Capturing moments and scenes through the art of taking pictures',
            'Guitar' => 'Playing and mastering the guitar instrument',
            'Piano' => 'Playing and mastering the piano instrument',
            'Painting' => 'Creating art through applying paint to a surface',
            'Cooking' => 'Preparing food by combining ingredients and applying heat',
            'Baking' => 'Making bread, cakes, and pastries using an oven',
            'Gardening' => 'Growing and caring for plants in a garden',
            'Hiking' => 'Walking for long distances in natural environments',
            'Yoga' => 'Physical, mental, and spiritual practices for health and relaxation',
            'Running' => 'Moving rapidly on foot for exercise or sport',
            'Chess' => 'Strategic board game played between two players',
            'Coding' => 'Writing computer programs and software development',
            'Knitting' => 'Creating fabric by interlocking loops of yarn with needles',
            'Dancing' => 'Moving rhythmically to music for artistic expression or enjoyment',
            'Writing' => 'Composing text for creative or informative purposes',
        ];
        
        // Goal templates for each hobby
        $goalTemplates = [
            'Photography' => [
                [
                    'goal' => 'Master DSLR Photography',
                    'description' => 'Learn to use a DSLR camera professionally',
                    'milestones' => [
                        'Learn about aperture and shutter speed',
                        'Practice portrait photography',
                        'Master lighting techniques'
                    ]
                ],
                [
                    'goal' => 'Build Photography Portfolio',
                    'description' => 'Create a professional portfolio of photographs',
                    'milestones' => [
                        'Take 100 high-quality photos',
                        'Select best 20 photos for portfolio',
                        'Create online portfolio website'
                    ]
                ]
            ],
            'Guitar' => [
                [
                    'goal' => 'Learn Basic Guitar Chords',
                    'description' => 'Master fundamental guitar chords',
                    'milestones' => [
                        'Learn A, D, E, G chords',
                        'Practice chord transitions',
                        'Play first complete song'
                    ]
                ]
            ],
            'Coding' => [
                [
                    'goal' => 'Build Web Application',
                    'description' => 'Create a full-stack web application',
                    'milestones' => [
                        'Design database schema',
                        'Create backend API',
                        'Build frontend interface',
                        'Deploy application'
                    ]
                ]
            ]
        ];
        
        // Default goal template for hobbies without specific templates
        $defaultGoalTemplate = [
            [
                'goal' => 'Improve Basic Skills',
                'description' => 'Master the fundamentals',
                'milestones' => [
                    'Research basic techniques',
                    'Practice regularly for 30 days',
                    'Demonstrate basic proficiency'
                ]
            ],
            [
                'goal' => 'Reach Intermediate Level',
                'description' => 'Advance beyond the basics',
                'milestones' => [
                    'Learn advanced techniques',
                    'Complete a challenging project',
                    'Receive feedback from experts'
                ]
            ]
        ];
        
        // Assign 2-4 random hobbies to each user
        foreach ($users as $user) {
            // Get random hobby keys
            $hobbyNames = array_rand($hobbies, rand(2, 4));
            
            // Ensure we have an array even if only one hobby is selected
            if (!is_array($hobbyNames)) {
                $hobbyNames = [$hobbyNames];
            }
            
            foreach ($hobbyNames as $hobbyName) {
                // Check if hobby already exists
                $existingHobby = Hobby::where('name', $hobbyName)->first();
                
                if (!$existingHobby) {
                    // Create new hobby with user_id and description
                    $hobby = new Hobby();
                    $hobby->name = $hobbyName;
                    $hobby->description = $hobbies[$hobbyName];
                    $hobby->user_id = $user->id;
                    $hobby->save();
                    
                    // Create goals and milestones for this hobby
                    $this->createGoalsForHobby($hobby, $hobbyName, $goalTemplates, $defaultGoalTemplate);
                }
            }
        }
    }
    
    private function createGoalsForHobby($hobby, $hobbyName, $goalTemplates, $defaultGoalTemplate)
    {
        // Get templates for this hobby or use default
        $templates = isset($goalTemplates[$hobbyName]) ? $goalTemplates[$hobbyName] : $defaultGoalTemplate;
        
        // Create 1-2 goals per hobby
        $numGoals = rand(1, 2);
        $selectedTemplates = array_slice($templates, 0, $numGoals);
        
        foreach ($selectedTemplates as $template) {
            $deadline = Carbon::now()->addDays(rand(30, 180));
            
            // Create goal
            $goal = new Goal();
            $goal->user_id = $hobby->user_id;
            $goal->hobby_id = $hobby->id;
            $goal->goal = $template['goal'];
            $goal->status = 'in-progress';
            $goal->progress = 0;
            $goal->deadline = $deadline;
            $goal->save();
            
            // Create milestones
            $milestoneCount = count($template['milestones']);
            $interval = $deadline->diffInDays(Carbon::now()) / ($milestoneCount + 1);
            
            foreach ($template['milestones'] as $index => $milestoneDesc) {
                $dueDate = Carbon::now()->addDays(ceil($interval * ($index + 1)));
                $completed = rand(0, 10) < 3; // 30% chance of being completed
                
                $milestone = new Milestone();
                $milestone->goal_id = $goal->id;
                $milestone->description = $milestoneDesc;
                $milestone->due_date = $dueDate;
                $milestone->completed = $completed;
                $milestone->save();
            }
            
            // Update goal progress based on completed milestones
            $this->updateGoalProgress($goal);
        }
    }
    
    private function updateGoalProgress($goal)
    {
        $milestones = $goal->milestones;
        $totalMilestones = $milestones->count();
        
        if ($totalMilestones > 0) {
            $completedMilestones = $milestones->where('completed', true)->count();
            $progress = round(($completedMilestones / $totalMilestones) * 100);
            $goal->progress = $progress;
            
            if ($progress == 100) {
                $goal->status = 'completed';
            }
            
            $goal->save();
        }
    }
}




