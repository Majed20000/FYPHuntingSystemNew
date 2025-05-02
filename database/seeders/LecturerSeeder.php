<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lecturer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class LecturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lecturers = [
            [
                'name' => 'Dr. John Lecturer',
                'email' => 'lecturer1@example.com',
                'staff_id' => 'STAFF002',
                'phone' => '0123456789',
                'research_group' => 'Artificial Intelligence'
            ],
            [
                'name' => 'Dr. Sarah Smith',
                'email' => 'lecturer2@example.com',
                'staff_id' => 'STAFF003',
                'phone' => '0123456790',
                'research_group' => 'Software Engineering'
            ],
            [
                'name' => 'Dr. Michael Wong',
                'email' => 'lecturer3@example.com',
                'staff_id' => 'STAFF004',
                'phone' => '0123456791',
                'research_group' => 'Data Science'
            ]
        ];

        foreach ($lecturers as $lecturerData) {
            DB::transaction(function () use ($lecturerData) {
                // Create user with lecturer role
                $user = User::create([
                    'name' => $lecturerData['name'],
                    'email' => $lecturerData['email'],
                    'matric_number' => $lecturerData['staff_id'],
                    'password' => Hash::make('password123'), // All lecturers have same password for testing
                    'role' => 'lecturer'
                ]);

                // Create lecturer record
                Lecturer::create([
                    'user_id' => $user->id,
                    'name' => $lecturerData['name'],
                    'staff_id' => $lecturerData['staff_id'],
                    'email' => $lecturerData['email'],
                    'phone' => $lecturerData['phone'],
                    'research_group' => $lecturerData['research_group'],
                    'max_students' => 5,
                    'current_students' => 0,
                    'accepting_students' => true
                ]);
            });
        }
    }
} 