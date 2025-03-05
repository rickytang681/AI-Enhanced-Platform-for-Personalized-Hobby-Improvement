<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Goal;
use App\Models\User;
use Carbon\Carbon;

class GoalSeeder extends Seeder
{
    public function run()
    {
        // Get all users including admin
        $users = User::all();

        // Sample hobbies with experience levels
        $hobbyOptions = [
            ['name' => 'Photography', 'experience' => 'Beginner'],
            ['name' => 'Guitar', 'experience' => 'Intermediate'],
            ['name' => 'Cooking', 'experience' => 'Advanced'],
            ['name' => 'Painting', 'experience' => 'Beginner'],
            ['name' => 'Writing', 'experience' => 'Intermediate'],
            ['name' => 'Dancing', 'experience' => 'Beginner'],
            ['name' => 'Gardening', 'experience' => 'Advanced'],
        ];

        // Sample goals
        $goalTemplates = [
            [
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
            [
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
            [
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
            // Create 2-3 goals for each user
            $numberOfGoals = rand(2, 3);
            
            for ($i = 0; $i < $numberOfGoals; $i++) {
                // Randomly select 1-2 hobbies for each goal
                $selectedHobbies = array_rand($hobbyOptions, rand(1, 2));
                if (!is_array($selectedHobbies)) {
                    $selectedHobbies = [$selectedHobbies];
                }
                
                $hobbies = array_map(function($index) use ($hobbyOptions) {
                    return $hobbyOptions[$index];
                }, $selectedHobbies);

                // Randomly select a goal template
                $template = $goalTemplates[array_rand($goalTemplates)];
                
                // Create the goal
                $goal = Goal::create([
                    'user_id' => $user->id,
                    'hobbies' => $hobbies,
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
                        'due_date' => now()->addDays(($index + 1) * 7), // Each milestone is a week apart
                        'completed' => rand(0, 1) == 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
}