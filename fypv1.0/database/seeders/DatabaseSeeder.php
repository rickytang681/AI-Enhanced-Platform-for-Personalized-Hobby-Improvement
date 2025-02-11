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
    }
}
