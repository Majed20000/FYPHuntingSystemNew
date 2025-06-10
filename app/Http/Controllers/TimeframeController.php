<?php

namespace App\Http\Controllers;

use App\Models\Timeframe;
use App\Models\Coordinator;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class TimeframeController extends Controller
{
    public function index()
    {
        $timeframes = Timeframe::orderBy('created_at', 'desc')->get();
        return view('coordinator.timeframes.index', compact('timeframes'));
    }

    public function create()
    {
        return view('coordinator.timeframes.add-timeframe');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'academic_year' => 'required|string|regex:/^\d{4}\/\d{4}$/', // Format: 2025/2026
                'semester' => 'required|in:1,2,3',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'proposal_submission_deadline' => 'required|date_format:Y-m-d\TH:i',
                'supervisor_confirmation_deadline' => 'required|date_format:Y-m-d\TH:i|after:proposal_submission_deadline',
                'max_applications_per_student' => 'required|integer|min:1|max:10',
                'max_appointments_per_student' => 'required|integer|min:1|max:10',
                'status' => 'required|in:draft,active,completed,archived',
                'is_active' => 'sometimes|boolean'
            ]);

            // Get the coordinator record for the authenticated user
            $coordinator = Coordinator::where('user_id', Auth::id())->first();

            if (!$coordinator) {
                throw new \Exception('Coordinator record not found');
            }

            // If setting this timeframe as active, deactivate all other timeframes
            if ($request->has('is_active') && $request->is_active) {
                Timeframe::where('is_active', true)->update(['is_active' => false]);

                // Store activation timestamp in cache to trigger notifications
                Cache::put('timeframe_activated_' . $request->academic_year . '_' . $request->semester, now()->timestamp);

                // Reset lecturer quotas to default when new timeframe is activated
                Lecturer::query()->update([
                    'quota' => config('app.default_lecturer_quota', 5), // Set your default quota in config
                    'updated_at' => now()
                ]);
            }

            // Format dates properly
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $proposalDeadline = Carbon::parse($request->proposal_submission_deadline);
            $supervisorDeadline = Carbon::parse($request->supervisor_confirmation_deadline);

            // Create new timeframe using coordinator's ID from coordinator table
            $timeframe = Timeframe::create([
                'coordinator_id' => $coordinator->id,
                'academic_year' => $request->academic_year,
                'semester' => $request->semester,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'proposal_submission_deadline' => $proposalDeadline,
                'supervisor_confirmation_deadline' => $supervisorDeadline,
                'max_applications_per_student' => $request->max_applications_per_student,
                'max_appointments_per_student' => $request->max_appointments_per_student,
                'status' => strtolower($request->status), // Ensure lowercase status
                'is_active' => $request->has('is_active') ? $request->is_active : false
            ]);

            return redirect()
                ->route('coordinator.timeframes.index')
                ->with('success', 'Timeframe created successfully');

        } catch (\Exception $e) {
            \Log::error('Error creating timeframe: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Error creating timeframe: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $timeframe = Timeframe::findOrFail($id);
        return view('coordinator.timeframes.edit', compact('timeframe'));
    }

    public function update(Request $request, $id)
    {
        try {
            $timeframe = Timeframe::findOrFail($id);

            $request->validate([
                'academic_year' => 'required|string|max:9',
                'semester' => 'required|in:1,2,3',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'proposal_submission_deadline' => 'required|date_format:Y-m-d\TH:i',
                'supervisor_confirmation_deadline' => 'required|date_format:Y-m-d\TH:i|after:proposal_submission_deadline',
                'max_applications_per_student' => 'required|integer|min:1|max:10',
                'max_appointments_per_student' => 'required|integer|min:1|max:10',
                'status' => 'required|in:draft,active,completed,archived',
                'is_active' => 'sometimes|boolean'
            ]);

            // If setting this timeframe as active, deactivate all other timeframes
            if ($request->has('is_active') && $request->is_active && !$timeframe->is_active) {
                Timeframe::where('is_active', true)->update(['is_active' => false]);

                // Store activation timestamp in cache to trigger notifications
                Cache::put('timeframe_activated_' . $timeframe->academic_year . '_' . $timeframe->semester, now()->timestamp);
            }

            $timeframe->update($request->all());

            return redirect()
                ->route('coordinator.timeframes.index')
                ->with('success', 'Timeframe updated successfully');

        } catch (\Exception $e) {
            \Log::error('Error updating timeframe: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Error updating timeframe: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $timeframe = Timeframe::findOrFail($id);

            if ($timeframe->is_active) {
                return back()->with('error', 'Cannot delete active timeframe');
            }

            $timeframe->delete();
            return back()->with('success', 'Timeframe deleted successfully');

        } catch (\Exception $e) {
            \Log::error('Error deleting timeframe: ' . $e->getMessage());
            return back()->with('error', 'Error deleting timeframe');
        }
    }

    public function getActiveTimeframe()
    {
        try {
            $activeTimeframe = Timeframe::where('is_active', true)
                ->where('status', 'active')
                ->first();

            $upcomingTimeframe = Timeframe::where('status', 'active')
                ->where('start_date', '>', now())
                ->orderBy('start_date', 'asc')
                ->first();

            $response = [
                'success' => true,
                'has_active' => false,
                'has_upcoming' => false
            ];

            if ($activeTimeframe) {
                $proposalDeadline = $activeTimeframe->proposal_submission_deadline;
                $response['has_active'] = true;
                $response['active_timeframe'] = [
                    'academic_year' => $activeTimeframe->academic_year,
                    'semester' => $activeTimeframe->semester,
                    'start_date' => $activeTimeframe->start_date->format('Y-m-d'),
                    'end_date' => $activeTimeframe->end_date->format('Y-m-d'),
                    'proposal_deadline' => $proposalDeadline->format('Y-m-d H:i:s'),
                    'supervisor_deadline' => $activeTimeframe->supervisor_confirmation_deadline->format('Y-m-d H:i:s'),
                    'days_to_proposal' => now()->diffInDays($proposalDeadline, false),
                    'hours_to_proposal' => now()->diffInHours($proposalDeadline, false) % 24,
                    'minutes_to_proposal' => now()->diffInMinutes($proposalDeadline, false) % 60,
                    'proposal_passed' => now()->greaterThan($proposalDeadline),
                    'max_applications_per_student' => $activeTimeframe->max_applications_per_student,
                    'max_appointments_per_student' => $activeTimeframe->max_appointments_per_student,
                    'activation_timestamp' => Cache::get('timeframe_activated_' . $activeTimeframe->academic_year . '_' . $activeTimeframe->semester),
                ];
            }

            if ($upcomingTimeframe) {
                $response['has_upcoming'] = true;
                $response['upcoming_timeframe'] = [
                    'academic_year' => $upcomingTimeframe->academic_year,
                    'semester' => $upcomingTimeframe->semester,
                    'start_date' => $upcomingTimeframe->start_date->format('Y-m-d'),
                    'end_date' => $upcomingTimeframe->end_date->format('Y-m-d'),
                    'proposal_deadline' => $upcomingTimeframe->proposal_submission_deadline->format('Y-m-d H:i:s'),
                    'supervisor_deadline' => $upcomingTimeframe->supervisor_confirmation_deadline->format('Y-m-d H:i:s'),
                    'days_until_start' => now()->diffInDays($upcomingTimeframe->start_date, false),
                    'max_applications_per_student' => $upcomingTimeframe->max_applications_per_student,
                    'max_appointments_per_student' => $upcomingTimeframe->max_appointments_per_student
                ];
            }

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Error getting timeframe information: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting timeframe information'
            ], 500);
        }
    }

    public function updateLecturerQuotas(Request $request, $timeframeId)
    {
        try {
            $timeframe = Timeframe::findOrFail($timeframeId);

            // Validate request
            $request->validate([
                'default_quota' => 'required|integer|min:1|max:20',
                'specific_quotas' => 'required|array',
                'specific_quotas.*.lecturer_id' => 'required|exists:lecturer,id',
                'specific_quotas.*.max_students' => 'required|integer|min:1|max:20',
                'specific_quotas.*.accepting_students' => 'required|boolean',
                'specific_quotas.*.notes' => 'nullable|string',
            ]);

            // Begin transaction
            \DB::beginTransaction();

            try {
                // Update each lecturer's quota, accepting status, and notes
                foreach ($request->specific_quotas as $quotaData) {
                    $updateData = [
                        'max_students' => $quotaData['max_students'],
                        'accepting_students' => $quotaData['accepting_students'],
                    ];
                    if (!empty($quotaData['updated_at'])) {
                        $updateData['updated_at'] = $quotaData['updated_at'];
                    } else {
                        $updateData['updated_at'] = now();
                    }
                    Lecturer::where('id', $quotaData['lecturer_id'])
                        ->update($updateData);
                }

                \DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Lecturer settings updated successfully'
                ]);

            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Error updating lecturer settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating lecturer settings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLecturerQuotas($timeframeId)
    {
        try {
            $timeframe = Timeframe::findOrFail($timeframeId);
            $lecturers = Lecturer::with('user')
                ->select('id', 'user_id', 'current_students', 'max_students', 'accepting_students', 'notes', 'updated_at')
                ->get()
                ->map(function ($lecturer) {
                    return [
                        'id' => $lecturer->id,
                        'name' => $lecturer->user->name,
                        'current_students' => $lecturer->current_students,
                        'max_students' => $lecturer->max_students,
                        'accepting_students' => $lecturer->accepting_students,
                        'notes' => $lecturer->notes,
                        'updated_at' => $lecturer->updated_at ? $lecturer->updated_at->format('d/m/Y H:i') : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'lecturers' => $lecturers,
                    'timeframe' => [
                        'id' => $timeframe->id,
                        'academic_year' => $timeframe->academic_year,
                        'semester' => $timeframe->semester
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting lecturer quotas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting lecturer quotas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showQuotaManager($timeframe)
    {
        $timeframe = Timeframe::findOrFail($timeframe);
        return view('coordinator.timeframes.manage-quotas', compact('timeframe'));
    }
}
