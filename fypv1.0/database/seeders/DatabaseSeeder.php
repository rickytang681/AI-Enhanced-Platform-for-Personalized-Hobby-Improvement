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
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create 10 Regular Users (increased from 5)
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
            ],
            // Add more users
            [
                'name' => 'Lisa Wong',
                'email' => 'lisa@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ],
            [
                'name' => 'James Miller',
                'email' => 'james@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ],
            [
                'name' => 'Olivia Garcia',
                'email' => 'olivia@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ],
            [
                'name' => 'Daniel Lee',
                'email' => 'daniel@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
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
        $this->call([
            LibrarySeeder::class,
            CommunitySeeder::class,
            // HobbySeeder now handles both hobbies and goals
            HobbySeeder::class,
        ]);
    }
}


