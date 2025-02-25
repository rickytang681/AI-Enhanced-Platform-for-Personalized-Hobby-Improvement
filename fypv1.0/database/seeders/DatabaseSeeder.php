<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\LibraryItem;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create Admin User
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('698321rtrh'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Regular User
        $userId = DB::table('users')->insertGetId([
            'name' => 'Ricky Tang',
            'email' => 'rickyt@gmail.com',
            'password' => Hash::make('698321rtrh'),
            'role' => 'user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Goals for Regular User
        $guitarGoalId = DB::table('goals')->insertGetId([
            'user_id' => $userId,
            'goal' => 'Master Guitar Playing',
            'hobbies' => json_encode([
                ['name' => 'Guitar', 'experience' => 'Beginner']
            ]),
            'deadline' => Carbon::now()->addMonths(6),
            'notes' => 'Want to learn acoustic guitar and be able to play basic songs',
            'progress' => 30,
            'status' => 'in-progress',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cookingGoalId = DB::table('goals')->insertGetId([
            'user_id' => $userId,
            'goal' => 'Learn Asian Cuisine',
            'hobbies' => json_encode([
                ['name' => 'Cooking', 'experience' => 'Intermediate']
            ]),
            'deadline' => Carbon::now()->addMonths(3),
            'notes' => 'Focus on Japanese and Thai dishes',
            'progress' => 50,
            'status' => 'in-progress',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Milestones for Guitar Goal
        DB::table('milestones')->insert([
            [
                'goal_id' => $guitarGoalId,
                'description' => 'Learn basic chords (A, D, G, C, Em, Am)',
                'due_date' => Carbon::now()->addMonth(),
                'completed' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'goal_id' => $guitarGoalId,
                'description' => 'Master basic strumming patterns',
                'due_date' => Carbon::now()->addMonths(2),
                'completed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'goal_id' => $guitarGoalId,
                'description' => 'Learn first complete song',
                'due_date' => Carbon::now()->addMonths(3),
                'completed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create Milestones for Cooking Goal
        DB::table('milestones')->insert([
            [
                'goal_id' => $cookingGoalId,
                'description' => 'Master making sushi rice',
                'due_date' => Carbon::now()->addWeeks(2),
                'completed' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'goal_id' => $cookingGoalId,
                'description' => 'Learn to make 3 different curry pastes',
                'due_date' => Carbon::now()->addMonths(1),
                'completed' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'goal_id' => $cookingGoalId,
                'description' => 'Perfect Pad Thai recipe',
                'due_date' => Carbon::now()->addMonths(2),
                'completed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create Library Items
        DB::table('library_items')->insert([
            [
                'user_id' => $userId, // Using the regular user we created above
                'title' => 'Beginner Guitar Chords Guide',
                'description' => 'A comprehensive guide for learning basic guitar chords with finger positions and practice tips.',
                'type' => 'text',
                'content' => "Learning guitar chords is essential for beginners. Here are the most important basic chords:

1. E Major (E)
- Place your first finger on the first fret of the G string
- Place your second finger on the second fret of the A string
- Place your third finger on the second fret of the D string

2. A Major (A)
- Place your first finger on the second fret of the B string
- Place your second finger on the second fret of the G string
- Place your third finger on the second fret of the D string

3. D Major (D)
- Place your first finger on the second fret of the G string
- Place your second finger on the second fret of the high E string
- Place your third finger on the third fret of the B string

Practice Tips:
- Start slowly and focus on clean sound
- Practice transitioning between chords
- Use a metronome to maintain rhythm
- Practice at least 15 minutes daily",
                'category' => 'Music',
                'subcategory' => 'Beginner',
                'likes' => 5,
                'dislikes' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'title' => 'Essential Thai Cooking Ingredients',
                'description' => 'A guide to must-have ingredients for Thai cooking with usage tips.',
                'type' => 'text',
                'content' => "To start cooking Thai food, you'll need these essential ingredients:

1. Fish Sauce (Nam Pla)
- Adds saltiness and umami
- Used in most Thai dishes
- Start with small amounts

2. Palm Sugar
- Adds sweetness with complex flavor
- Can substitute with brown sugar
- Essential for balancing spicy dishes

3. Thai Basil
- Different from Italian basil
- Used in stir-fries and curries
- Adds distinctive Thai flavor

4. Coconut Milk
- Base for curries
- Use full-fat for best results
- Shake well before using

5. Lemongrass
- Used in soups and curries
- Remove outer layers
- Bruise before using

Storage Tips:
- Keep fish sauce at room temperature
- Store palm sugar in airtight container
- Freeze unused lemongrass

Remember: Thai cooking is about balancing sweet, sour, salty, and spicy flavors.",
                'category' => 'Cooking',
                'subcategory' => 'Intermediate',
                'likes' => 8,
                'dislikes' => 0,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => $adminId, // Using the admin user
                'title' => 'Photography Basics: Understanding Exposure',
                'description' => 'Learn the fundamentals of exposure in photography with this beginner-friendly guide.',
                'type' => 'text',
                'content' => "Understanding exposure is crucial for photography. The exposure triangle consists of:

1. Aperture (f-stop)
- Controls depth of field
- Lower f-number = more light, blurrier background
- Higher f-number = less light, sharper background
- Example: f/1.8 for portraits, f/8 for landscapes

2. Shutter Speed
- Controls motion blur
- Faster speeds freeze action
- Slower speeds create motion effects
- Rule of thumb: 1/focal length for handheld

3. ISO
- Controls light sensitivity
- Lower ISO = less noise, better quality
- Higher ISO = more noise, better in low light
- Start with ISO 100 in daylight

Practice Exercise:
1. Set your camera to manual mode
2. Start with ISO 100
3. Choose f/8 aperture
4. Adjust shutter speed until exposure is correct
5. Take test shots and observe differences

Remember: Good exposure is about balancing these three elements based on your creative goals.",
                'category' => 'Photography',
                'subcategory' => 'Beginner',
                'likes' => 12,
                'dislikes' => 2,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
        ]);

        // Create some reactions for the library items
        DB::table('library_reactions')->insert([
            [
                'user_id' => $adminId,
                'library_item_id' => 1,
                'reaction_type' => 'like',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'library_item_id' => 2,
                'reaction_type' => 'like',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $adminId,
                'library_item_id' => 3,
                'reaction_type' => 'like',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create some library items with different types
        $libraryItems = [
            [
                'user_id' => $userId,
                'title' => 'Guitar Basics: Getting Started',
                'description' => 'A comprehensive guide for beginners learning guitar.',
                'type' => 'text',
                'content' => "Here's how to get started with guitar:
                    1. Learn basic chords (A, D, G)
                    2. Practice finger placement
                    3. Start with simple strumming patterns
                    4. Learn your first song",
                'category' => 'Music',
                'subcategory' => 'Beginner',
                'likes' => 5,
                'dislikes' => 1,
                'created_at' => now()->subDays(5),
                'average_rating' => 4.5,
                'rating_count' => 2
            ],
            [
                'user_id' => $adminId,
                'title' => 'Cooking Thai Curry',
                'description' => 'Learn to make authentic Thai curry from scratch',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/embed/example',
                'category' => 'Cooking',
                'subcategory' => 'Intermediate',
                'likes' => 8,
                'dislikes' => 0,
                'created_at' => now()->subDays(3),
                'average_rating' => 5,
                'rating_count' => 3
            ],
            [
                'user_id' => $userId,
                'title' => 'Photography Composition Tips',
                'description' => 'Master the art of photo composition',
                'type' => 'text',
                'content' => "Essential composition rules:
                    1. Rule of thirds
                    2. Leading lines
                    3. Symmetry and patterns
                    4. Frame within frame",
                'category' => 'Photography',
                'subcategory' => 'Advanced',
                'likes' => 12,
                'dislikes' => 2,
                'created_at' => now()->subDay(),
                'average_rating' => 4.2,
                'rating_count' => 5
            ]
        ];

        foreach ($libraryItems as $item) {
            $libraryItem = LibraryItem::create($item);

            // Add comments
            $comments = [
                [
                    'user_id' => $adminId,
                    'content' => 'This is really helpful! Thanks for sharing.',
                    'created_at' => now()->subHours(12)
                ],
                [
                    'user_id' => $userId,
                    'content' => 'Great resource, exactly what I was looking for.',
                    'created_at' => now()->subHours(6)
                ]
            ];

            foreach ($comments as $comment) {
                $libraryItem->comments()->create($comment);
            }

            // Add ratings
            $ratings = [
                ['user_id' => $adminId, 'rating' => 5],
                ['user_id' => $userId, 'rating' => 4]
            ];

            foreach ($ratings as $rating) {
                $libraryItem->ratings()->create($rating);
            }

            // Add saves (previously favorites)
            $libraryItem->saves()->create([
                'user_id' => $adminId
            ]);

            // Add reactions
            $reactions = [
                ['user_id' => $adminId, 'reaction_type' => 'like'],
                ['user_id' => $userId, 'reaction_type' => 'like']
            ];

            foreach ($reactions as $reaction) {
                $libraryItem->reactions()->create($reaction);
            }
        }

        // Create additional users and their interactions
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user'
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user'
            ]
        ];

        foreach ($users as $userData) {
            $user = DB::table('users')->insertGetId($userData + [
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Add random interactions for each library item
            LibraryItem::all()->each(function($item) use ($user) {
                // Random rating
                if (rand(0, 1)) {
                    $item->ratings()->create([
                        'user_id' => $user,
                        'rating' => rand(3, 5)
                    ]);
                }

                // Random save (previously favorite)
                if (rand(0, 1)) {
                    $item->saves()->create([
                        'user_id' => $user
                    ]);
                }

                // Random comment
                if (rand(0, 1)) {
                    $item->comments()->create([
                        'user_id' => $user,
                        'content' => 'This is a great resource! Thanks for sharing.',
                        'created_at' => now()->subHours(rand(1, 24))
                    ]);
                }

                // Random reaction
                if (rand(0, 1)) {
                    $item->reactions()->create([
                        'user_id' => $user,
                        'reaction_type' => rand(0, 1) ? 'like' : 'dislike'
                    ]);
                }
            });
        }
    }
}
