<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('698321rtrh'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ricky Tang',
                'email' => 'rickyt@gmail.com',
                'password' => Hash::make('698321rtrh'),
                'role' => 'user', 
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
