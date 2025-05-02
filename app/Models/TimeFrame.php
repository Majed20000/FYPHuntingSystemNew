<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timeframe extends Model
{
    protected $table = 'timeframe';

    protected $fillable = [
        'coordinator_id',
        'semester',
        'academic_year',
        'start_date',
        'end_date',
        'is_active',
        'max_applications_per_student',
        'max_appointments_per_student',
        'proposal_submission_deadline',
        'supervisor_confirmation_deadline',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'proposal_submission_deadline' => 'datetime',
        'supervisor_confirmation_deadline' => 'datetime',
        'is_active' => 'boolean',
        'max_applications_per_student' => 'integer',
        'max_appointments_per_student' => 'integer'
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ARCHIVED = 'archived';

    // Semester constants
    const SEMESTER_1 = '1';
    const SEMESTER_2 = '2';
    const SEMESTER_3 = '3';

    /**
     * Get the coordinator that owns the timeframe.
     */
    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(Coordinator::class, 'coordinator_id', 'user_id');
    }

    /**
     * Get appointments within this timeframe.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Scope a query to only include active timeframes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the timeframe is currently active.
     */
    public function isActive(): bool
    {
        return $this->is_active && 
               now()->between($this->start_date, $this->end_date) && 
               $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if proposal submission is still open.
     */
    public function isProposalSubmissionOpen(): bool
    {
        return now()->lessThan($this->proposal_submission_deadline);
    }

    /**
     * Check if supervisor confirmation is still open.
     */
    public function isSupervisorConfirmationOpen(): bool
    {
        return now()->lessThan($this->supervisor_confirmation_deadline);
    }

    /**
     * Get the current academic year and semester.
     */
    public static function getCurrentPeriod()
    {
        return self::where('is_active', true)
            ->where('status', self::STATUS_ACTIVE)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    /**
     * Format the academic year for display.
     */
    public function getFormattedAcademicYearAttribute(): string
    {
        return $this->academic_year . ' Semester ' . $this->semester;
    }
}
