<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    //Lecturer Dashboard
    public function index()
    {
        try {
            // Get the user
            $user = Auth::user();
            
            // Check if user is a lecturer
            if (!$user->isLecturer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. User is not a lecturer.'
                ], 403);
            }

            // Get the appointments for the Lecturer where lecturer_id is the user id, 
            // Where the appointment date is greater than or equal to today
            $appointments = Appointment::where('lecturer_id', $user->id)
                ->where('appointment_date', '>=', Carbon::today())
                ->get()
                ->map(function ($appointment) {
                    return [
                        'id' => $appointment->id,
                        'title' => $appointment->status === 'available' ? 'Available' : 'Booked',
                        'start' => $appointment->appointment_date . 'T' . $appointment->start_time,
                        'end' => $appointment->appointment_date . 'T' . $appointment->end_time,
                        'className' => $appointment->status === 'available' ? 'available-event' : 'booked-event',
                        'extendedProps' => [
                            'status' => $appointment->status,
                            'student_name' => $appointment->student ? $appointment->student->name : null,
                            'title' => $appointment->title,
                            'description' => $appointment->description,
                            'meeting_type' => $appointment->meeting_type
                        ]
                    ];
                });

            return response()->json($appointments);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading appointments: ' . $e->getMessage()
            ], 500);
        }
    }

    //Lecturer Create Appointment
    public function createAppointmentSlot(Request $request, $user_id)
    {
        try {
            // Verify the lecturer
            if (!auth()->user()->isLecturer() || auth()->id() != $user_id) {
                return redirect()->back()->with('error', 'Unauthorized access');
            }

            // Validate request
            $request->validate([
                'appointment_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
            ]);

            // Create appointment using user_id directly
            $appointment = Appointment::create([
                'lecturer_id' => $user_id,  // Use user_id directly
                'appointment_date' => $request->appointment_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => 'available'
            ]);

            return redirect()->back()->with('success', 'Appointment slot created successfully');

        } catch (\Exception $e) {
            \Log::error('Error creating appointment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating appointment: ' . $e->getMessage());
        }
    }

    //Lecturer Delete Appointment
    public function deleteAppointmentSlot($user_id, $id)
    {
        try {
            // Get lecturer record
            $lecturer = \App\Models\Lecturer::where('user_id', $user_id)->firstOrFail();

            // Find appointment using user_id instead of lecturer id
            $appointment = Appointment::where('lecturer_id', $lecturer->user_id) // Changed from $lecturer->id
                ->where('id', $id)
                ->where('status', 'available')
                ->firstOrFail();

            $appointment->delete();

            return redirect()
                ->route('lecturer.calendar', ['user_id' => $user_id])
                ->with('success', 'Appointment slot deleted successfully');

        } catch (\Exception $e) {
            \Log::error('Error deleting appointment: ' . $e->getMessage());
            return redirect()
                ->route('lecturer.calendar', ['user_id' => $user_id])
                ->with('error', 'Error deleting appointment: ' . $e->getMessage());
        }
    }

    //Student View Available Slots for all Lecturers
    public function getAvailableSlots(Request $request, $user_id, $lecturer_id = null)
    {
        try {
            // Verify the student
            if (!auth()->user()->isStudent() || auth()->id() != $user_id) {
                return redirect()->back()->with('error', 'Unauthorized access');
            }

            // Build query with proper eager loading
            $query = Appointment::with([
                'lecturer' => function($query) {
                    $query->with('user'); // Eager load the user relationship
                }
            ])
            ->where('status', 'available')
            ->where('appointment_date', '>=', now()->toDateString());

            // Filter by lecturer if specified
            if ($lecturer_id) {
                $query->where('lecturer_id', $lecturer_id);
            }

            // Debug the SQL query
            \Log::info('SQL Query:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            // Get appointments sorted by date and time
            $appointments = $query->orderBy('appointment_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();

            // Debug each appointment's relationships
            foreach ($appointments as $appointment) {
                \Log::info('Appointment Details:', [
                    'id' => $appointment->id,
                    'lecturer_id' => $appointment->lecturer_id,
                    'raw_lecturer' => $appointment->lecturer,  // Add this to see raw lecturer data
                    'lecturer_user_id' => $appointment->lecturer ? $appointment->lecturer->user_id : 'no lecturer',
                    'has_lecturer' => $appointment->lecturer ? 'yes' : 'no',
                    'has_user' => $appointment->lecturer && $appointment->lecturer->user ? 'yes' : 'no',
                    'user_name' => $appointment->lecturer && $appointment->lecturer->user ? $appointment->lecturer->user->name : 'n/a',
                    'appointment_date' => $appointment->appointment_date,
                    'start_time' => $appointment->start_time,
                    'end_time' => $appointment->end_time
                ]);
            }

            // Group appointments by Lecturer id
            $groupedAppointments = $appointments->groupBy('lecturer_id');

            // Return the view with the grouped appointments
            return view('student.view-slots', [
                'user_id' => $user_id,
                'appointments' => $groupedAppointments,
                'selectedLecturerId' => $lecturer_id,
                'debug_raw_appointments' => $appointments // Add this for debugging
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getAvailableSlots: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error loading appointments: ' . $e->getMessage());
        }
    }

    //Student Book Appointment
    public function bookAppointment(Request $request, $user_id, $id)
    {
        try {
            // Verify the student
            if (!auth()->user()->isStudent() || auth()->id() != $user_id) {
                return redirect()->back()->with('error', 'Unauthorized access');
            }

            // Validate request with custom messages
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'meeting_type' => 'required|in:online,in-person',
                'meeting_link' => 'required_if:meeting_type,online|nullable|url'
            ], [
                'meeting_type.in' => 'The selected meeting type must be either online or in-person.',
                'meeting_link.required_if' => 'The meeting link is required when meeting type is online.',
                'meeting_link.url' => 'The meeting link must be a valid URL.'
            ]);

            // Get Student record
            $student = \App\Models\Student::where('user_id', $user_id)->firstOrFail();

            // Check appointment limits
            $pendingCount = Appointment::where('student_id', $student->id)
                ->where('status', 'pending')
                ->count();

            $approvedCount = Appointment::where('student_id', $student->id)
                ->where('status', 'approved')
                ->count();

            if ($pendingCount >= 3) {
                return redirect()->back()->with('error', 'You cannot book more appointments. You already have 3 pending appointments.');
            }

            if ($approvedCount >= 3) {
                return redirect()->back()->with('error', 'You cannot book more appointments. You already have 3 approved appointments.');
            }

            $appointment = Appointment::findOrFail($id);

            if ($appointment->status !== 'available') {
                return redirect()->back()->with('error', 'This slot is no longer available');
            }

            // Check for time overlap with student's existing appointments
            $existingAppointments = Appointment::where('student_id', $student->id)
                ->where('appointment_date', $appointment->appointment_date)
                ->where('status', '!=', 'rejected')
                ->where(function($query) use ($appointment) {
                    $query->where(function($q) use ($appointment) {
                        // New appointment starts during an existing appointment
                        $q->where('start_time', '<=', $appointment->start_time)
                          ->where('end_time', '>', $appointment->start_time);
                    })->orWhere(function($q) use ($appointment) {
                        // New appointment ends during an existing appointment
                        $q->where('start_time', '<', $appointment->end_time)
                          ->where('end_time', '>=', $appointment->end_time);
                    })->orWhere(function($q) use ($appointment) {
                        // New appointment completely contains an existing appointment
                        $q->where('start_time', '>=', $appointment->start_time)
                          ->where('end_time', '<=', $appointment->end_time);
                    });
                })
                ->first();

            if ($existingAppointments) {
                return redirect()->back()->with('error', 'You already have an appointment scheduled during this time slot.');
            }

            // Prepare appointment data
            $appointmentData = [
                'student_id' => $student->id,
                'title' => $request->title,
                'description' => $request->description,
                'meeting_type' => $request->meeting_type,
                'status' => 'pending'
            ];

            // Only include meeting_link if meeting type is online
            if ($request->meeting_type === 'online') {
                $appointmentData['meeting_link'] = $request->meeting_link;
            } else {
                $appointmentData['meeting_link'] = null; // Explicitly set to null for in-person meetings
            }

            // Update appointment
            $appointment->update($appointmentData);

            // Log successful booking
            \Log::info('Appointment booked successfully', [
                'student_id' => $student->id,
                'appointment_id' => $id,
                'pending_count' => $pendingCount + 1,
                'approved_count' => $approvedCount,
                'meeting_type' => $request->meeting_type
            ]);

            return redirect()->back()->with('success', 'Appointment booking request submitted successfully');

        } catch (\Exception $e) {
            \Log::error('Error booking appointment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error booking appointment: ' . $e->getMessage());
        }
    }

    //Finished
    public function showAppointments($user_id)
    {
        try {
            $user = Auth::user();
            
            // Check if user is authorized
            if (!$user->isLecturer() || $user->id != $user_id) {
                return redirect()->back()->with('error', 'Unauthorized access');
            }

            // Get appointments using user_id directly
            $appointments = Appointment::where('lecturer_id', $user_id)  // Use user_id directly
                ->with(['student.user'])
                ->where('appointment_date', '>=', Carbon::today())
                ->orderBy('appointment_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();

            return view('lecturer.calendar', [
                'user_id' => $user_id,
                'appointments' => $appointments
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading appointments: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading appointments');
        }
    }

    //Finished
    public function acceptAppointment(Request $request, $user_id, $id)
    {
        try {
            // Verify the lecturer
            if (!auth()->user()->isLecturer() || auth()->id() != $user_id) {
                return redirect()->back()->with('error', 'Unauthorized access');
            }

            $appointment = Appointment::with(['student.user', 'lecturer.user'])->findOrFail($id);

            // Verify the appointment belongs to this lecturer
            if ($appointment->lecturer_id != $user_id) {
                return redirect()->back()->with('error', 'Unauthorized access to this appointment');
            }

            // Verify appointment is in pending status
            if ($appointment->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending appointments can be accepted');
            }

            // Update appointment status
            $appointment->update([
                'status' => 'approved'
            ]);

            // Send email notifications
            try {
                // Send email to student
                \Mail::to($appointment->student->user->email)
                    ->send(new \App\Mail\AppointmentAccepted($appointment, 'student'));

                // Send email to lecturer
                \Mail::to($appointment->lecturer->user->email)
                    ->send(new \App\Mail\AppointmentAccepted($appointment, 'lecturer'));

                \Log::info('Appointment acceptance emails sent successfully', [
                    'appointment_id' => $id,
                    'student_email' => $appointment->student->user->email,
                    'lecturer_email' => $appointment->lecturer->user->email
                ]);
            } catch (\Exception $e) {
                \Log::error('Error sending appointment acceptance emails: ' . $e->getMessage());
                // Don't return error - continue with success message as appointment was approved
            }

            return redirect()->back()->with('success', 'Appointment has been approved successfully and notifications have been sent.');

        } catch (\Exception $e) {
            \Log::error('Error accepting appointment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error accepting appointment');
        }
    }

    //Finished
    public function rejectAppointment(Request $request, $user_id, $id)
    {
        try {
            // Verify the lecturer
            if (!auth()->user()->isLecturer() || auth()->id() != $user_id) {
                return redirect()->back()->with('error', 'Unauthorized access');
            }

            // Validate rejection reason
            $request->validate([
                'rejection_reason' => 'required|string|max:255'
            ]);

            $appointment = Appointment::findOrFail($id);

            // Verify the appointment belongs to this lecturer
            if ($appointment->lecturer_id != $user_id) {
                return redirect()->back()->with('error', 'Unauthorized access to this appointment');
            }

            // Verify appointment is in pending status
            if ($appointment->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending appointments can be rejected');
            }

            // Update appointment
            $appointment->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason
            ]);

            return redirect()->back()->with('success', 'Appointment has been rejected');

        } catch (\Exception $e) {
            \Log::error('Error rejecting appointment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error rejecting appointment');
        }
    }

    //Finished
    public function viewStudentAppointments($user_id)
    {
        try {
            // Verify the student
            if (!auth()->user()->isStudent() || auth()->id() != $user_id) {
                return redirect()->back()->with('error', 'Unauthorized access');
            }

            // Get student record
            $student = \App\Models\Student::where('user_id', $user_id)->firstOrFail();

            // Get all appointments for this student
            $appointments = Appointment::where('student_id', $student->id)
                ->with(['lecturer.user']) // Eager load lecturer relationship
                ->orderBy('appointment_date', 'desc')
                ->orderBy('start_time', 'desc')
                ->get()
                ->groupBy('status'); // Group appointments by status

            return view('student.view-appointments', [
                'user_id' => $user_id,
                'appointments' => $appointments
            ]);

        } catch (\Exception $e) {
            \Log::error('Error viewing appointments: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading appointments');
        }
    }
}
