<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LecturerController
{
    public function createLecturerRecord()
    {
        try {
            $user = Auth::user();
            
            if (!$user->isLecturer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. User is not a lecturer.'
                ], 403);
            }

            // Check if lecturer record already exists
            if ($user->lecturer) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lecturer record already exists'
                ]);
            }

            // Create lecturer record
            $lecturer = Lecturer::create([
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lecturer record created successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating lecturer record: ' . $e->getMessage()
            ], 500);
        }
    }
} 