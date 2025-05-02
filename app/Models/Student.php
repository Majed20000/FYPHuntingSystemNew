<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'student'; //To set the table name

    //To fill the attributes
    protected $fillable = [
        'user_id',
        'name',
        'matric_id',
        'email',
        'phone',
        'program'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Supervisor (Lecturer)
    public function supervisor()
    {
        return $this->belongsToMany(Lecturer::class, 'supervise')
            ->withTimestamps();
    }

    // Relationship with Project Proposals
    public function projectProposals()
    {
        return $this->hasMany(ProjectProposal::class);
    }

    // Relationship with Appointments
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // Relationship with Timetable Slots
    public function timetableSlots()
    {
        return $this->hasMany(TimetableSlot::class);
    }

    // Check if student has a supervisor
    public function hasSupervisor()
    {
        return $this->supervisor()->exists();
    }

    // Get current project proposal
    public function getCurrentProposal()
    {
        return $this->projectProposals()
            ->where('status', 'approved')
            ->latest()
            ->first();
    }

    // Get upcoming appointments
    public function getUpcomingAppointments()
    {
        return $this->appointments()
            ->where('appointment_date', '>=', now())
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get();
    }

    // Get total number of pending applications
    public function getPendingApplicationsCount()
    {
        return $this->projectProposals()
            ->where('status', 'pending')
            ->count();
    }

    // Check if student can submit more applications
    public function canSubmitMoreApplications($maxApplications = 3)
    {
        $pendingCount = $this->getPendingApplicationsCount();
        return $pendingCount < $maxApplications;
    }
}
