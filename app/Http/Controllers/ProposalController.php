<?php

namespace App\Http\Controllers;

// Import the necessary classes
use App\Models\ProjectProposal;
use App\Models\Lecturer;
use App\Models\Timeframe;
// use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;

class ProposalController extends Controller
{
    //Student browse lecturer proposal
    public function browse(Request $request, $user_id)
    {
        // Verify user authorization
        if (!Auth::user()->isStudent() || Auth::id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Fetch the proposals from the database
        $query = ProjectProposal::with(['lecturer', 'lecturer.user', 'timeframe', 'student.user'])
            ->where('proposal_type', 'lecturer')
            ->where(function ($q) {
                $q->where('status', 'available')
                    ->orWhere('status', 'unavailable')
                    ->orWhere('status', 'pending')
                    ->orWhere('status', 'approved');
            });

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply lecturer filter
        if ($request->has('lecturer') && $request->lecturer) {
            $query->where('lecturer_id', $request->lecturer);
        }

        // Apply status filter
        if ($request->has('status') && $request->status) {
            if ($request->status === 'unavailable') {
                $query->where(function ($q) {
                    $q->where('status', 'unavailable')
                        ->orWhere('status', 'pending')
                        ->orWhere('status', 'approved');
                });
            } else {
                $query->where('status', $request->status);
            }
        }

        // Fetch the proposals from the database
        $proposals = $query->latest()->paginate(10);
        $lecturers = Lecturer::with('user')->get();

        // Debug information
        \Log::info('Browse Proposals Query:', [
            'status_filter' => $request->status,
            'count' => $proposals->count(),
            'proposals' => $proposals->map(function ($proposal) {
                return [
                    'id' => $proposal->id,
                    'title' => $proposal->title,
                    'status' => $proposal->status,
                    'student_name' => optional($proposal->student)->user->name ?? 'No Student',
                ];
            })
        ]);

        // Return the view with the proposals and lecturers
        return view('student.browseProposal', compact('proposals', 'lecturers'));
    }

    // Student apply for proposal
    public function updateApplication(Request $request, $user_id)
    {
        // Verify user authorization
        if (!Auth::user()->isStudent() || Auth::id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Validate incoming request data
            $request->validate([
                'proposal_id' => 'required|exists:projectproposal,id',
                'lecturer_id' => 'required|exists:lecturer,id'
            ]);


            // Start a database transaction
            DB::beginTransaction();

            // Get student ID from the authenticated user
            $student = Auth::user()->student;
            if (!$student) {
                return redirect()->back()->with('error', 'Student profile not found.');
            }

            // Check if student already has pending or approved applications
            $existingApplications = ProjectProposal::where('student_id', $student->id)
                ->whereIn('status', ['pending', 'approved'])
                ->first();


            // If the student already has pending or approved applications, roll back the transaction and return an error message
            if ($existingApplications) {
                DB::rollBack();
                if ($existingApplications->status === 'approved') {
                    return redirect()->back()->with('error', 'You already have an approved proposal. You cannot apply for another one.');
                } else {
                    return redirect()->back()->with('error', 'You already have a pending application. Please wait for the response or withdraw your current application before applying to another proposal.');
                }
            }

            // Fetch the proposal from the database
            $proposal = ProjectProposal::where('id', $request->proposal_id)
                ->where('lecturer_id', $request->lecturer_id)
                ->where('status', 'available')
                ->first();

            // If the proposal is not found or not available, roll back the transaction and return an error message
            if (!$proposal) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Proposal not found or not available.');
            }

            // Update the proposal
            $proposal->update([
                'student_id' => $student->id,
                'status' => 'pending'
            ]);

            // Commit the transaction
            DB::commit();

            // Log the successful application
            \Log::info('Application submitted successfully', [
                'proposal_id' => $proposal->id,
                'student_id' => $student->id,
                'lecturer_id' => $request->lecturer_id
            ]);

            // Redirect to the browse proposals page with a success message
            return redirect()->back()->with('success', 'Application submitted successfully. Please wait for the lecturer\'s response.');
        } catch (\Exception $e) {
            // Roll back the transaction if an error occurs
            DB::rollBack();
            // Log the error
            \Log::error('Error submitting application: ' . $e->getMessage());
            // Redirect to the browse proposals page with an error message
            return redirect()->back()->with('error', 'Failed to submit application. Please try again.');
        }
    }

    //Student view their application
    public function myApplications(Request $request, $user_id)
    {
        // Verify user authorization
        if (!Auth::user()->isStudent() || Auth::id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }

        $student = Auth::user()->student;

        // Get applications with eager loading of lecturer relationship
        $applications = ProjectProposal::with(['lecturer' => function ($query) {
            $query->select('id', 'name', 'email', 'staff_id', 'max_students', 'user_id');
        }])
            ->where('student_id', $student->id)
            ->whereNotNull('student_id')
            ->latest()
            ->paginate(10);

        // Debug information
        \Log::info('Applications:', [
            'count' => $applications->count(),
            'lecturer_ids' => $applications->pluck('lecturer_id')->toArray()
        ]);

        // Get all lecturers for debugging
        $lecturerIds = $applications->pluck('lecturer_id')->unique();
        $lecturers = Lecturer::whereIn('id', $lecturerIds)->get();

        // Log the lecturers found
        \Log::info('Lecturers found:', [
            'count' => $lecturers->count(),
            'lecturer_details' => $lecturers->map(function ($lecturer) {
                return [
                    'id' => $lecturer->id,
                    'name' => $lecturer->name,
                    'staff_id' => $lecturer->staff_id
                ];
            })
        ]);

        return view('student.myapplication', compact('applications', 'lecturers'));
    }

    //Lecturer's Proposal

    public function manageProposals($user_id)
    {
        // Verify user is a lecturer
        if (!Auth::user()->lecturer || Auth::id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }

        $lecturer = Auth::user()->lecturer;
        $lecturer1 = Auth::user()->lecturer;

        // Fetch the proposals from the database
        $proposals = ProjectProposal::where('lecturer_id', $lecturer->id, $lecturer1->user_id)
            ->where('proposal_type', 'lecturer')
            ->with(['timeframe']) // Eager load timeframe relationship
            ->latest()
            ->paginate(10);

        return view('lecturer.manageProposal', compact('proposals', 'lecturer'));
    }

    // Create a new proposal
    public function store(Request $request, $user_id)
    {
        // Verify user is a lecturer
        if (!Auth::user()->lecturer || Auth::id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Validate the incoming request data
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:10',
            ]);

            // Check for duplicate title
            $existingProposal = ProjectProposal::where('title', $request->title)
                ->where('proposal_type', 'lecturer')
                ->first();

            if ($existingProposal) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'A proposal with this title already exists. Please choose a different title.');
            }

            // Get the active timeframe
            $currentTimeframe = \App\Models\Timeframe::where('is_active', true)
                ->where('status', 'active')
                ->first();

            // If no active timeframe is found, redirect back with an error message
            if (!$currentTimeframe) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'No active timeframe found. Please contact the administrator.');
            }

            // Create a new proposal
            ProjectProposal::create([
                'lecturer_id' => Auth::user()->lecturer->id,
                'title' => $request->title,
                'description' => $request->description,
                'timeframe_id' => $currentTimeframe->id,
                'proposal_type' => 'lecturer',
                'status' => 'available'
            ]);

            // Redirect to manage proposals page with success message
            return redirect()
                ->route('lecturer.proposals.manage', ['user_id' => $user_id])
                ->with('success', 'Proposal created successfully!');

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating proposal: ' . $e->getMessage());
            
            // Redirect back with error message
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create proposal. Please try again.');
        }
    }

    // Lecturer update proposal 
    public function update(Request $request, $user_id, ProjectProposal $proposal)
    {
        // Verify user is a lecturer and owns the proposal
        if (!Auth::user()->lecturer || Auth::id() != $user_id || $proposal->lecturer_id != Auth::user()->lecturer->id) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the incoming request data
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'status' => 'required|in:available,unavailable'
        ]);

        // Keep the existing timeframe_id when updating
        $proposal->update([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'timeframe_id' => $proposal->timeframe_id // Preserve existing timeframe
        ]);

        return redirect()->back()->with('success', 'Proposal updated successfully');
    }

    // Lecturer delete proposal
    public function destroy($user_id, ProjectProposal $proposal)
    {
        // Verify user is a lecturer and owns the proposal
        if (!Auth::user()->lecturer || Auth::id() != $user_id || $proposal->lecturer_id != Auth::user()->lecturer->id) {
            abort(403, 'Unauthorized action.');
        }

        $proposal->delete();

        return redirect()->back()->with('success', 'Proposal deleted successfully');
    }
    // Lecturer manage applications
    public function manageApplications($user_id)
    {
        // Verify user is a lecturer
        if (!Auth::user()->lecturer || Auth::id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }

        $query = ProjectProposal::where('lecturer_id', Auth::user()->lecturer->id)
            ->whereNotNull('student_id')  // Only get proposals with students
            ->with(['student.user'])  // Eager load student and user relationships
            ->where(function ($q) {
                $q->where('status', 'pending')
                    ->orWhere('status', 'approved')
                    ->orWhere('status', 'rejected');
            });

        // Apply status filter
        if (request()->has('status') && request()->status) {
            $query->where('status', request()->status);
        }

        // Apply proposal title filter
        if (request()->has('proposal') && request()->proposal) {
            $query->where('title', 'like', '%' . request()->proposal . '%');
        }

        $applications = $query->latest()->paginate(10);

        // Debug information
        \Log::info('Applications Query:', [
            'lecturer_id' => Auth::user()->lecturer->id,
            'count' => $applications->count(),
            'applications' => $applications->map(function ($app) {
                return [
                    'id' => $app->id,
                    'title' => $app->title,
                    'status' => $app->status,
                    'student_name' => optional($app->student)->user->name ?? 'No Student',
                ];
            })
        ]);

        return view('lecturer.manageApplication', compact('applications'));
    }

    public function updateApplicationStatus(Request $request, $user_id, ProjectProposal $proposal)
    {
        // Verify user is a student and owns the application
        if (!Auth::user()->student || Auth::id() != $user_id || $proposal->student_id != Auth::user()->student->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            // Check if student already has pending or approved applications
            $existingApplications = ProjectProposal::where('student_id', Auth::user()->student->id)
                ->where('id', '!=', $proposal->id)
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            // If the student already has pending or approved applications, roll back the transaction and return an error message
            if ($existingApplications) {
                DB::rollBack();
                if ($existingApplications->status === 'approved') {
                    return redirect()->back()->with('error', 'You already have an approved proposal. You cannot apply for another one.');
                } else {
                    return redirect()->back()->with('error', 'You already have a pending application. Please wait for the response or withdraw your current application before applying to another proposal.');
                }
            }

            // Validate the incoming request data
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:10',
                'status' => 'required|in:available,unavailable'
            ]);

            // Keep the existing timeframe_id when updating
            $proposal->update([
                'student_id' => $user_id,
                'status' => 'pending',
                'timeframe_id' => $proposal->timeframe_id // Preserve existing timeframe
            ]);

            DB::commit();

            // Log the successful application
            \Log::info('Student application updated:', [
                'student_id' => $user_id,
                'proposal_id' => $proposal->id,
                'status' => 'pending'
            ]);

            // Redirect to the manage applications page with a success message
            return redirect()
                ->route('lecturer.applications.manage', ['user_id' => $user_id])
                ->with('success', 'Application submitted successfully. Please wait for the lecturer\'s response.');
        } catch (\Exception $e) {
            // Roll back the transaction if an error occurs
            DB::rollBack();
            // Log the error
            \Log::error('Error updating application status: ' . $e->getMessage(), [
                'student_id' => $user_id,
                'proposal_id' => $proposal->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update application. Please try again later.');
        }
    }

    // Lecturer create proposal
    public function create($user_id)
    {
        // Verify user is a lecturer
        if (!Auth::user()->lecturer || Auth::id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('lecturer.createProposal');
    }

    // Lecturer edit proposal
    public function edit($user_id, ProjectProposal $proposal)
    {
        // Verify user is a lecturer and owns the proposal
        if (!Auth::user()->lecturer || Auth::id() != $user_id || $proposal->lecturer_id != Auth::user()->lecturer->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('lecturer.editProposal', compact('proposal'));
    }

    // Lecturer show proposal
    public function show($user_id, ProjectProposal $proposal)
    {
        // Verify user is a lecturer and owns the proposal
        if (!Auth::user()->lecturer || Auth::id() != $user_id || $proposal->lecturer_id != Auth::user()->lecturer->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('lecturer.showProposal', compact('proposal'));
    }

    // Lecturer review application
    public function reviewApplication(Request $request, $user_id, ProjectProposal $proposal)
    {
        // Verify user is a lecturer and owns the proposal
        if (!Auth::user()->lecturer || Auth::id() != $user_id || $proposal->lecturer_id != Auth::user()->lecturer->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Validate the incoming request data
            $request->validate([
                'status' => 'required|in:approved,rejected',
                'rejection_reason' => 'required_if:status,rejected|string|nullable'
            ]);

            DB::beginTransaction();

            // Debug log before update
            \Log::info('Reviewing application:', [
                'proposal_id' => $proposal->id,
                'current_status' => $proposal->status,
                'new_status' => $request->status,
                'has_rejection_reason' => !empty($request->rejection_reason),
                'request_data' => $request->all()
            ]);

            // Get the lecturer
            $lecturer = Auth::user()->lecturer;

            // If approving and not already approved
            if ($request->status === 'approved' && $proposal->status !== 'approved') {
                // Check if lecturer has reached their quota
                if ($lecturer->current_students >= $lecturer->max_students) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Cannot approve application. You have reached your maximum student quota.');
                }

                // Check if student already has an approved proposal
                $existingApprovedProposal = ProjectProposal::where('student_id', $proposal->student_id)
                    ->where('id', '!=', $proposal->id)
                    ->where('status', 'approved')
                    ->first();

                // If the student already has an approved proposal, roll back the transaction and return an error message
                if ($existingApprovedProposal) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Cannot approve application. Student already has an approved proposal.');
                }

                // Increment current_students
                $lecturer->increment('current_students');

                // Create supervision record
                try {
                    DB::table('supervise')->insert([
                        'lecturer_id' => $lecturer->id,
                        'student_id' => $proposal->student_id,
                        'proposal_id' => $proposal->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Log the supervision record created
                    \Log::info('Supervision record created:', [
                        'lecturer_id' => $lecturer->id,
                        'student_id' => $proposal->student_id,
                        'proposal_id' => $proposal->id
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Error creating supervision record: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Error creating supervision record. Please try again.');
                }

                // Update all other pending applications of this student to rejected
                ProjectProposal::where('student_id', $proposal->student_id)
                    ->where('id', '!=', $proposal->id)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'rejected',
                        'rejection_reason' => 'Another proposal has been approved'
                    ]);
            }
            // If changing from approved to rejected, decrement the count and remove supervision record
            elseif ($request->status === 'rejected' && $proposal->status === 'approved') {
                $lecturer->decrement('current_students');

                // Remove supervision record
                DB::table('supervise')
                    ->where('lecturer_id', $lecturer->id)
                    ->where('student_id', $proposal->student_id)
                    ->where('proposal_id', $proposal->id)
                    ->delete();

                // Log the supervision record removed
                \Log::info('Supervision record removed:', [
                    'lecturer_id' => $lecturer->id,
                    'student_id' => $proposal->student_id,
                    'proposal_id' => $proposal->id
                ]);
            }

            // Update the proposal status
            $proposal->update([
                'status' => $request->status,
                'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null
            ]);

            DB::commit();

            // Log the successful application review
            \Log::info('Application review completed:', [
                'proposal_id' => $proposal->id,
                'new_status' => $proposal->status,
                'reviewed_by' => Auth::id(),
                'updated_at' => $proposal->updated_at,
                'lecturer_current_students' => $lecturer->current_students
            ]);

            // Redirect to the manage applications page with a success message
            return redirect()->route('lecturer.applications.manage', ['user_id' => $user_id])
                ->with('success', 'Application has been ' . $request->status . ' successfully');
        } catch (\Exception $e) {
            // Roll back the transaction if an error occurs
            DB::rollBack();
            \Log::error('Error reviewing application: ' . $e->getMessage(), [
                'proposal_id' => $proposal->id,
                'user_id' => $user_id,
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Redirect to the manage applications page with an error message
            return redirect()->back()
                ->with('error', 'Failed to submit review. Please try again. Error: ' . $e->getMessage());
        }
    }

    public function showApplication($user_id, ProjectProposal $application)
    {
        // Verify user is a lecturer and owns the proposal
        if (!Auth::user()->lecturer || Auth::id() != $user_id || $application->lecturer_id != Auth::user()->lecturer->id) {
            abort(403, 'Unauthorized action.');
        }

        // Load the necessary relationships
        $application->load(['student.user', 'timeframe']);

        return view('lecturer.showApplication', compact('application'));
    }
}
