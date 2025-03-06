<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Goal;
use App\Models\User;
use App\Models\Hobby;
use Carbon\Carbon;

class GoalSeeder extends Seeder
{
    public function run()
    {
        // Get all users including admin
        $users = User::all();

        // Sample goals by hobby type
        $goalTemplates = [
            'Photography' => [
                'goal' => 'Master basic photography techniques',
                'notes' => 'Focus on composition and lighting',
                'deadline' => now()->addMonths(3),
                'milestones' => [
                    'Learn camera basics and settings',
                    'Practice composition techniques',
                    'Understanding lighting',
                    'Complete first photoshoot'
                ]
            ],
            'Guitar' => [
                'goal' => 'Learn to play 5 songs on guitar',
                'notes' => 'Practice daily for at least 30 minutes',
                'deadline' => now()->addMonths(2),
                'milestones' => [
                    'Master basic chords',
                    'Learn first song',
                    'Practice strumming patterns',
                    'Record a cover song'
                ]
            ],
            'Cooking' => [
                'goal' => 'Cook 10 different international dishes',
                'notes' => 'Focus on Asian and Mediterranean cuisine',
                'deadline' => now()->addMonths(1),
                'milestones' => [
                    'Learn basic cooking techniques',
                    'Master 3 basic recipes',
                    'Try advanced cooking methods',
                    'Host a dinner party'
                ]
            ]
        ];

        foreach ($users as $user) {
            // Get existing hobbies for the user
            $hobbies = $user->hobbies;
            
            if ($hobbies->isNotEmpty()) {
                // Create 1-2 goals for each hobby
                foreach ($hobbies as $hobby) {
                    $numberOfGoals = rand(1, 2);
                    
                    for ($i = 0; $i < $numberOfGoals; $i++) {
                        // Get the template based on hobby name, or use a default one
                        $template = $goalTemplates[$hobby->name] ?? array_values($goalTemplates)[0];
                        
                        // Create the goal
                        $goal = Goal::create([
                            'user_id' => $user->id,
                            'hobby_id' => $hobby->id,
                            'goal' => $template['goal'],
                            'notes' => $template['notes'],
                            'deadline' => $template['deadline'],
                            'progress' => rand(0, 100),
                            'status' => rand(0, 1) ? 'in-progress' : 'completed',
                            'created_at' => now()->subDays(rand(1, 30)),
                            'updated_at' => now()
                        ]);

                        // Create milestones for the goal
                        foreach ($template['milestones'] as $index => $milestone) {
                            $goal->milestones()->create([
                                'description' => $milestone,
                                'due_date' => now()->addDays(($index + 1) * 7),
                                'completed' => rand(0, 1) == 1,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
            }
        }
    }
}
