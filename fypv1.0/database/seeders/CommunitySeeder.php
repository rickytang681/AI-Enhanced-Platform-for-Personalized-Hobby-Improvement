<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Community;
use App\Models\User;
use Carbon\Carbon;

class CommunitySeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $allUserIds = $users->pluck('id')->toArray();
        $userCount = count($allUserIds);

        $communityPosts = [
            [
                'title' => 'Tips for Improving Photography Skills',
                'content' => 'I\'ve been practicing photography for 6 months now. Here are some tips that helped me improve:
                    1. Practice composition daily
                    2. Learn to use manual mode
                    3. Study lighting techniques
                    4. Take lots of practice shots
                    What tips would you add?',
                'post_type' => 'experience',
                'tag' => 'Photography'
            ],
            [
                'title' => 'Guitar Learning Resources',
                'content' => 'Can anyone recommend good online resources for learning guitar? I\'m a complete beginner and looking for structured learning materials.',
                'post_type' => 'question',
                'tag' => 'Music'
            ],
            [
                'title' => 'My Writing Journey',
                'content' => 'Started creative writing last year and wanted to share my progress. The biggest challenge was developing a daily writing habit, but it\'s getting easier now.',
                'post_type' => 'experience',
                'tag' => 'Writing'
            ],
            [
                'title' => 'Gardening Tips for Beginners',
                'content' => 'Just started my first garden! Looking for advice on:
                    - Best plants for beginners
                    - Watering schedule
                    - Basic tools needed
                    Any help appreciated!',
                'post_type' => 'question',
                'tag' => 'Gardening'
            ],
            [
                'title' => 'Discussion: Digital vs Traditional Art',
                'content' => 'What are your thoughts on digital vs traditional art? Both have their advantages, but I\'d love to hear your experiences and preferences.',
                'post_type' => 'discussion',
                'tag' => 'Art'
            ],
            [
                'title' => 'Cooking Challenge: One New Recipe Per Week',
                'content' => 'I challenged myself to learn one new recipe every week. It\'s been amazing for improving my cooking skills! Anyone want to join this challenge?',
                'post_type' => 'experience',
                'tag' => 'Cooking'
            ],
            [
                'title' => 'Best Way to Learn Dance Online?',
                'content' => 'With so many online dance classes available, which ones would you recommend? Looking for beginner-friendly options.',
                'post_type' => 'question',
                'tag' => 'Dancing'
            ],
            [
                'title' => 'Photography Equipment Discussion',
                'content' => 'Let\'s discuss essential photography equipment for beginners. What should be the first investments after getting a basic camera?',
                'post_type' => 'discussion',
                'tag' => 'Photography'
            ],
            [
                'title' => 'Looking for photography buddies in NYC',
                'content' => 'Hi everyone! I\'m looking for fellow photographers to explore NYC with. Anyone interested in a weekend photowalk?',
                'post_type' => 'discussion',
                'tag' => 'Photography'
            ],
            [
                'title' => 'Guitar string recommendations?',
                'content' => 'I\'ve been playing acoustic guitar for about a year now and looking to replace my strings. Any recommendations for a warm, rich tone?',
                'post_type' => 'question',
                'tag' => 'Music'
            ],
            [
                'title' => 'Share your latest painting!',
                'content' => 'Let\'s inspire each other! Share your latest painting and tell us about your process.',
                'post_type' => 'discussion',
                'tag' => 'Art'
            ],
            [
                'title' => 'Best cookbooks for beginners?',
                'content' => 'I\'m just starting my cooking journey and looking for beginner-friendly cookbooks. What would you recommend?',
                'post_type' => 'question',
                'tag' => 'Cooking'
            ],
            [
                'title' => 'Yoga retreat experiences',
                'content' => 'Has anyone been to a yoga retreat? I\'m considering booking one for next summer and would love to hear about your experiences.',
                'post_type' => 'question',
                'tag' => 'Fitness'
            ],
            [
                'title' => 'Chess tournament this weekend',
                'content' => 'I\'m organizing an online chess tournament this weekend. All skill levels welcome! Comment if you\'re interested.',
                'post_type' => 'discussion',
                'tag' => 'Games'
            ],
            [
                'title' => 'Learning React vs Angular',
                'content' => 'I\'m a beginner in web development and trying to decide between learning React or Angular first. Any advice?',
                'post_type' => 'question',
                'tag' => 'Technology'
            ],
            [
                'title' => 'Knitting pattern exchange',
                'content' => 'Let\'s exchange our favorite knitting patterns! I\'ll start - I love this simple beanie pattern: [link]',
                'post_type' => 'discussion',
                'tag' => 'Arts & Crafts'
            ],
            [
                'title' => 'Dance studio recommendations in Chicago',
                'content' => 'Recently moved to Chicago and looking for a good dance studio that offers various styles. Any recommendations?',
                'post_type' => 'question',
                'tag' => 'Dancing'
            ],
            [
                'title' => 'Writing prompt challenge',
                'content' => 'Weekly writing prompt: Write a short story that begins with "The door opened to reveal..."',
                'post_type' => 'discussion',
                'tag' => 'Writing'
            ],
        ];

        $comments = [
            "Great insights! Thanks for sharing.",
            "I've had similar experiences. Keep it up!",
            "This is really helpful advice.",
            "Thanks for starting this discussion.",
            "I completely agree with your points.",
            "Let me share what worked for me...",
            "Interesting perspective! Never thought about it that way.",
            "This motivated me to start my journey.",
            "Really useful tips for beginners.",
            "Looking forward to more posts like this!"
        ];

        foreach ($communityPosts as $postData) {
            // Create community post with random user
            $post = Community::create([
                'title' => $postData['title'],
                'content' => $postData['content'],
                'post_type' => $postData['post_type'],
                'tag' => $postData['tag'],
                'user_id' => $allUserIds[array_rand($allUserIds)],
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30))
            ]);

            // Add random reactions (likes/dislikes)
            $numReactions = min(rand(2, 5), $userCount); // Ensure we don't exceed user count
            $reactionUsers = array_rand($allUserIds, $numReactions);
            if (!is_array($reactionUsers)) {
                $reactionUsers = [$reactionUsers];
            }
            
            foreach ($reactionUsers as $userId) {
                $post->reactions()->create([
                    'user_id' => $allUserIds[$userId],
                    'reaction_type' => rand(0, 4) > 0 ? 'like' : 'dislike', // 80% chance of like
                    'created_at' => now()->subDays(rand(1, 15))
                ]);
            }

            // Add random comments
            $numComments = min(rand(1, 3), $userCount); // Ensure we don't exceed user count
            $commentUsers = array_rand($allUserIds, $numComments);
            if (!is_array($commentUsers)) {
                $commentUsers = [$commentUsers];
            }
            
            foreach ($commentUsers as $userId) {
                $post->comments()->create([
                    'user_id' => $allUserIds[$userId],
                    'content' => $comments[array_rand($comments)],
                    'created_at' => now()->subDays(rand(1, 15))
                ]);
            }

            // Add random saves
            $numSaves = min(rand(1, 2), $userCount); // Ensure we don't exceed user count
            $saveUsers = array_rand($allUserIds, $numSaves);
            if (!is_array($saveUsers)) {
                $saveUsers = [$saveUsers];
            }
            
            foreach ($saveUsers as $userId) {
                $post->saves()->create([
                    'user_id' => $allUserIds[$userId],
                    'created_at' => now()->subDays(rand(1, 15))
                ]);
            }
        }
    }
}


