<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LibraryItem;
use App\Models\User;
use Carbon\Carbon;

class LibrarySeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $allUserIds = $users->pluck('id')->toArray();

        $libraryItems = [
            [
                'title' => 'Getting Started with Photography',
                'description' => 'A comprehensive guide for beginners in photography.',
                'type' => 'text',
                'content' => 'Learn the basics of photography including camera settings, composition, and lighting techniques.',
                'category' => 'Photography',
                'subcategory' => 'Beginner'
            ],
            [
                'title' => 'Basic Guitar Chords',
                'description' => 'Essential chords every beginner guitarist should know.',
                'type' => 'video',
                'content' => 'https://example.com/guitar-chords-tutorial',
                'category' => 'Music',
                'subcategory' => 'Guitar'
            ],
            [
                'title' => 'Watercolor Painting Techniques',
                'description' => 'Master the fundamentals of watercolor painting.',
                'type' => 'text',
                'content' => 'Explore various watercolor techniques including washes, glazing, and dry brush.',
                'category' => 'Art',
                'subcategory' => 'Painting'
            ],
            [
                'title' => 'Creative Writing Workshop',
                'description' => 'Improve your creative writing skills.',
                'type' => 'text',
                'content' => 'Learn about character development, plot structure, and narrative techniques.',
                'category' => 'Writing',
                'subcategory' => 'Creative'
            ],
            [
                'title' => 'Dance Fundamentals',
                'description' => 'Basic dance moves and rhythms.',
                'type' => 'video',
                'content' => 'https://example.com/dance-basics',
                'category' => 'Dance',
                'subcategory' => 'Beginner'
            ],
            [
                'title' => 'Gardening for Beginners',
                'description' => 'Start your gardening journey.',
                'type' => 'text',
                'content' => 'Learn about soil preparation, planting techniques, and plant care basics.',
                'category' => 'Gardening',
                'subcategory' => 'Beginner'
            ],
            [
                'title' => 'Advanced Cooking Techniques',
                'description' => 'Master professional cooking methods.',
                'type' => 'video',
                'content' => 'https://example.com/advanced-cooking',
                'category' => 'Cooking',
                'subcategory' => 'Advanced'
            ],
            [
                'title' => 'Digital Photography Tips',
                'description' => 'Advanced techniques for digital photography.',
                'type' => 'text',
                'content' => 'Advanced topics in digital photography including post-processing and special effects.',
                'category' => 'Photography',
                'subcategory' => 'Advanced'
            ]
        ];

        $comments = [
            "This is really helpful! Thanks for sharing.",
            "Great resource, exactly what I needed.",
            "Very well explained, thank you!",
            "This helped me a lot with my learning.",
            "Excellent content, keep it up!",
            "Thanks for putting this together.",
            "Very informative and well-structured.",
            "This is perfect for beginners!",
            "Advanced concepts explained simply.",
            "Looking forward to more content like this!"
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
                    'rating' => $rating,
                    'created_at' => now()->subDays(rand(1, 15))
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
                    'reaction_type' => rand(0, 4) > 0 ? 'like' : 'dislike', // 80% chance of like
                    'created_at' => now()->subDays(rand(1, 15))
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
                    'user_id' => $allUserIds[$userId],
                    'created_at' => now()->subDays(rand(1, 15))
                ]);
            }
        }
    }
}