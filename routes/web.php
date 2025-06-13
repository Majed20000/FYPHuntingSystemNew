<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\TimeframeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\UserRegisterController;

// Welcome Page Route
Route::get('/', function () {
    return view('welcome');
});

// Route to Login Page
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Protected Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Direct to Dashboard Routes for each roles
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isStudent()) {
            return redirect()->route('student.dashboard', ['user_id' => $user->id]);
        } elseif ($user->isLecturer()) {
            return redirect()->route('lecturer.dashboard', ['user_id' => $user->id]);
        } elseif ($user->isCoordinator()) {
            return redirect()->route('coordinator.dashboard');
        } else {
            return redirect('/');
        }
    })->name('dashboard');

    //User Logout
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    // Student Routes Group
    Route::prefix('student/{user_id}')->name('student.')->group(function () {
        Route::get('/dashboard', function ($user_id) {
            // Check if the user is a student and if the user id is the same as the one in url
            if (!auth()->user()->isStudent() || auth()->id() != $user_id) {
                abort(403, 'Unauthorized action.');
            }

            return view('student.dashboard', [

                'user_id' => $user_id,
                'lecturers' => \App\Models\Lecturer::with('user')->get()
            ]);
        })->name('dashboard');

        // View Available Slots
        Route::get('/view-slots/{lecturer_id?}', [AppointmentController::class, 'getAvailableSlots'])
            ->name('view-slots');

        // Student Book Appointment
        Route::post('/appointments/{id}/book', [AppointmentController::class, 'bookAppointment'])
            ->name('appointments.book');

        // Student View Their Appointments
        Route::get('/appointments', [AppointmentController::class, 'viewStudentAppointments'])
            ->name('appointments.view');

        // Browse Proposals
        Route::get('/browse-proposals', [ProposalController::class, 'browse'])
            ->name('browse-proposals');

        // Update Proposal
        Route::post('/update-proposal', [ProposalController::class, 'updateApplication'])
            ->name('update-proposal');

        // My Applications
        Route::get('/my-applications', [ProposalController::class, 'myApplications'])
            ->name('my-applications');
    });

    // Lecturer Routes Group
    Route::prefix('lecturer/{user_id}')->name('lecturer.')->group(function () {
        Route::get('/dashboard', function ($user_id) {
            // Check if the user is a lecturer and if the user id is the same as the one in url
            if (!auth()->user()->isLecturer() || auth()->id() != $user_id) {
                abort(403, 'Unauthorized action.');
            }
            //
            return view('lecturer.dashboard', ['user_id' => $user_id]);
        })->name('dashboard');

        // Lecturer View Appointment
        Route::get('/calendar', [AppointmentController::class, 'showAppointments'])->name('calendar');

        // Lecturer Create Appointment
        Route::post('/appointments', [AppointmentController::class, 'createAppointmentSlot'])->name('appointments.store');

        // Lecturer Delete Appointment
        Route::delete('/appointments/{id}', [AppointmentController::class, 'deleteAppointmentSlot'])->name('appointments.delete');

        // Lecturer Accept Appointment
        Route::post('/appointments/{id}/accept', [AppointmentController::class, 'acceptAppointment'])
            ->name('appointments.accept');

        // Lecturer Reject Appointment
        Route::post('/appointments/{id}/reject', [AppointmentController::class, 'rejectAppointment'])
            ->name('appointments.reject');

        // Application management routes - Qurr
        Route::get('/applications', [ProposalController::class, 'manageApplications'])
            ->name('applications.manage');

        Route::put('/applications/{proposal}/review', [ProposalController::class, 'reviewApplication'])
            ->name('applications.review');

        Route::put('/applications/{proposal}', [ProposalController::class, 'updateApplicationStatus'])
            ->name('applications.update');

        // Proposal management routes
        Route::get('/proposals', [ProposalController::class, 'manageProposals'])
            ->name('proposals.manage');
    });

    // Proposal Management Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/lecturer/{user_id}/proposals', [ProposalController::class, 'manageProposals'])->name('lecturer.proposals.manage');
        Route::post('/lecturer/{user_id}/proposals', [ProposalController::class, 'store'])->name('lecturer.proposals.store');
        Route::put('/lecturer/{user_id}/proposals/{proposal}', [ProposalController::class, 'update'])->name('lecturer.proposals.update');
        Route::delete('/lecturer/{user_id}/proposals/{proposal}', [ProposalController::class, 'destroy'])->name('lecturer.proposals.destroy');
        Route::get('/lecturer/{user_id}/proposals/create', [ProposalController::class, 'create'])->name('lecturer.proposals.create');
        Route::get('/lecturer/{user_id}/proposals/{proposal}/edit', [ProposalController::class, 'edit'])->name('lecturer.proposals.edit');
        Route::get('/lecturer/{user_id}/proposals/{proposal}', [ProposalController::class, 'show'])->name('lecturer.proposals.show');
    });

    // Coordinator Routes Group
    Route::prefix('coordinator')->name('coordinator.')->group(function () {
        Route::get('/dashboard', function () {

            if (!auth()->user()->isCoordinator()) {
                abort(403, 'Unauthorized action.');
            }
            return view('coordinator.dashboard');
        })->name('dashboard');

        // User Registration Routes for Coordinator
        Route::get('/register', [UserRegisterController::class, 'index'])->name('userRegister');

        Route::post('/register', [UserRegisterController::class, 'store'])
            ->name('register.store');

        Route::delete('/register/{id}', [UserRegisterController::class, 'destroy'])
            ->name('register.destroy');


        // Timeframes routes
        Route::prefix('timeframes')->name('timeframes.')->group(function () {
            Route::post('{timeframe}/quotas/update', [TimeframeController::class, 'updateLecturerQuotas'])
                ->name('quotas.update');

            Route::get('{timeframe}/quotas/data', [TimeframeController::class, 'getLecturerQuotas'])
                ->name('quotas.data');

            Route::get('{timeframe}/quotas/manage', [TimeframeController::class, 'showQuotaManager'])
                ->name('quotas.manage');

            Route::get('{timeframe}/quotas/summary', [TimeframeController::class, 'showQuotaSummary'])
                ->name('quotas.summary');

            Route::get('/', [TimeframeController::class, 'index'])->name('index');
            Route::get('/create', [TimeframeController::class, 'create'])->name('create');
            Route::post('/', [TimeframeController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [TimeframeController::class, 'edit'])->name('edit');
            Route::put('/{id}', [TimeframeController::class, 'update'])->name('update');
            Route::delete('/{id}', [TimeframeController::class, 'destroy'])->name('destroy');
        });

        Route::post('/timeframes/{timeframe}/quotas', [TimeframeController::class, 'updateLecturerQuotas'])
            ->name('coordinator.timeframes.quotas.update');

        Route::get('/timeframes/{timeframe}/quotas', [TimeframeController::class, 'getLecturerQuotas'])
            ->name('coordinator.timeframes.quotas.index');
    });

    // Change Password Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/change-password', [App\Http\Controllers\Auth\LoginController::class, 'showChangePasswordForm'])->name('password.change.form');
        Route::post('/change-password', [App\Http\Controllers\Auth\LoginController::class, 'changePassword'])->name('password.change');
    });
});

// Add this route for debugging
Route::get('/debug/appointments', function () {
    $appointments = \App\Models\Appointment::with('lecturer.user')
        ->where('status', 'available')
        ->where('appointment_date', '>=', now()->toDateString())
        ->get();

    return response()->json([
        'count' => $appointments->count(),
        'appointments' => $appointments
    ]);
});

// Add this with your public routes
Route::get('/timeframe/active', [TimeframeController::class, 'getActiveTimeframe'])->name('timeframe.active');
