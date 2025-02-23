<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
    }
}
