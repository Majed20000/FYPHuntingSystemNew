<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\Coordinator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CoordinatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Create user with coordinator role
            $user = User::create([
                'name' => 'Dr. Sarah Coordinator',
                'email' => 'coordinator@example.com',
                'password' => Hash::make('password123'),
                'role' => 'coordinator'
            ]);

            // Create lecturer record
            $lecturer = Lecturer::create([
                'user_id' => $user->id,
                'name' => 'Dr. Sarah Coordinator',
                'staff_id' => 'STAFF001',
                'email' => 'coordinator@example.com',
                'phone' => '0123456789',
                'research_group' => 'Software Engineering',
                'max_students' => 5,
                'current_students' => 0,
                'accepting_students' => true
            ]);

            // Create coordinator record
            Coordinator::create([
                'user_id' => $user->id,
                'lecturer_id' => $lecturer->id
            ]);
        });
    }
} 