<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\LibraryItem;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
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

        // Create 5 Regular Users
        $users = [
            [
                'name' => 'John Smith',
                'email' => 'john@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'user'
            ],
            [
                'name' => 'Emma Wilson',
                'email' => 'emma@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'user'
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'user'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'user'
            ],
            [
                'name' => 'David Brown',
                'email' => 'david@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'user'
            ],
            [
                'name' => 'Ricky Tang',
                'email' => 'rickyt@gmail.com',
                'password' => Hash::make('698321rtrh'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        $userIds = [];
        foreach ($users as $user) {
            $userIds[] = DB::table('users')->insertGetId(array_merge($user, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // All available user IDs (admin + regular users)
        $allUserIds = array_merge([$adminId], $userIds);

        // Create Library Items with interactions
        $libraryItems = [
            [
                'title' => 'Getting Started with Photography',
                'description' => 'A comprehensive guide for beginners in photography.',
                'type' => 'text',
                'content' => "Essential photography tips for beginners:\n1. Learn about exposure triangle\n2. Practice composition rules\n3. Understanding lighting\n4. Camera settings basics",
                'category' => 'Photography',
                'subcategory' => 'Beginner'
            ],
            [
                'title' => 'Advanced Guitar Techniques',
                'description' => 'Master advanced guitar playing techniques.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/embed/example1',
                'category' => 'Music',
                'subcategory' => 'Advanced'
            ],
            [
                'title' => 'Cooking Basics: Essential Skills',
                'description' => 'Learn fundamental cooking techniques and skills.',
                'type' => 'text',
                'content' => "Basic cooking skills everyone should know:\n1. Knife skills\n2. Heat control\n3. Seasoning basics\n4. Kitchen organization",
                'category' => 'Cooking',
                'subcategory' => 'Beginner'
            ]
        ];

        foreach ($libraryItems as $itemData) {
            // Create library item with random user
            $item = LibraryItem::create(array_merge($itemData, [
                'user_id' => $allUserIds[array_rand($allUserIds)],
                'likes' => 0,
                'dislikes' => 0,
                'average_rating' => 0,
                'rating_count' => 0,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30))
            ]));

            // Add random ratings (3-5 ratings per item)
            $ratingUsers = array_rand($allUserIds, rand(3, 5));
            if (!is_array($ratingUsers)) {
                $ratingUsers = [$ratingUsers];
            }
            
            foreach ($ratingUsers as $userId) {
                $rating = rand(3, 5); // Ratings between 3-5 stars
                $item->ratings()->create([
                    'user_id' => $allUserIds[$userId],
                    'rating' => $rating
                ]);
            }

            // Update average rating
            $averageRating = $item->ratings()->avg('rating');
            $ratingCount = $item->ratings()->count();
            $item->update([
                'average_rating' => round($averageRating, 1),
                'rating_count' => $ratingCount
            ]);

            // Add random reactions (likes/dislikes)
            $reactionUsers = array_rand($allUserIds, rand(3, 6));
            if (!is_array($reactionUsers)) {
                $reactionUsers = [$reactionUsers];
            }
            
            foreach ($reactionUsers as $userId) {
                $item->reactions()->create([
                    'user_id' => $allUserIds[$userId],
                    'reaction_type' => rand(0, 4) > 0 ? 'like' : 'dislike' // 80% chance of like
                ]);
            }

            // Update likes/dislikes count
            $item->update([
                'likes' => $item->reactions()->where('reaction_type', 'like')->count(),
                'dislikes' => $item->reactions()->where('reaction_type', 'dislike')->count()
            ]);

            // Add random comments
            $commentUsers = array_rand($allUserIds, rand(2, 4));
            if (!is_array($commentUsers)) {
                $commentUsers = [$commentUsers];
            }
            
            $comments = [
                "This is really helpful! Thanks for sharing.",
                "Great resource, exactly what I needed.",
                "Very well explained, thank you!",
                "This helped me a lot with my learning.",
                "Excellent content, keep it up!",
                "Thanks for putting this together.",
                "Very informative and well-structured."
            ];

            foreach ($commentUsers as $userId) {
                $item->comments()->create([
                    'user_id' => $allUserIds[$userId],
                    'content' => $comments[array_rand($comments)],
                    'created_at' => now()->subDays(rand(1, 15))
                ]);
            }

            // Add random saves
            $saveUsers = array_rand($allUserIds, rand(2, 4));
            if (!is_array($saveUsers)) {
                $saveUsers = [$saveUsers];
            }
            
            foreach ($saveUsers as $userId) {
                $item->saves()->create([
                    'user_id' => $allUserIds[$userId]
                ]);
            }
        }
    }
}
