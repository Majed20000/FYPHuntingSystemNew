<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $table = 'appointment';

    protected $fillable = [
        'lecturer_id',
        'student_id',
        'timeslot_id',
        'timeframe_id',
        'title',
        'description',
        'meeting_link',
        'meeting_type',
        'status',
        'rejection_reason',
        'appointment_date',
        'start_time',
        'end_time'
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_UNAVAILABLE = 'unavailable';
    const STATUS_REJECTED = 'rejected';

    // Meeting type constants
    const TYPE_ONLINE = 'online';
    const TYPE_PHYSICAL = 'physical';

    /**
     * Get the lecturer associated with the appointment.
     */
    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id', 'user_id');
    }

    /**
     * Get the student associated with the appointment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id')->with('user');
    }

    /**
     * Get the timeslot associated with the appointment.
     */
    public function timeslot(): BelongsTo
    {
        return $this->belongsTo(TimetableSlot::class, 'timeslot_id');
    }

    /**
     * Get the timeframe associated with the appointment.
     */
    public function timeframe(): BelongsTo
    {
        return $this->belongsTo(Timeframe::class, 'timeframe_id');
    }

    /**
     * Scope a query to only include pending appointments.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved appointments.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
                    ->orderBy('appointment_date', 'asc')
                    ->orderBy('start_time', 'asc');
    }

    /**
     * Check if the appointment can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    /**
     * Check if the appointment is online.
     */
    public function isOnline(): bool
    {
        return $this->meeting_type === self::TYPE_ONLINE;
    }

    /**
     * Get the appointment duration in minutes.
     */
    public function getDurationInMinutes(): int
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        return $end->diffInMinutes($start);
    }

    /**
     * Format the appointment time range.
     */
    public function getTimeRangeAttribute(): string
    {
        return sprintf(
            '%s - %s',
            \Carbon\Carbon::parse($this->start_time)->format('H:i'),
            \Carbon\Carbon::parse($this->end_time)->format('H:i')
        );
    }

    /**
     * Check if the appointment overlaps with another appointment.
     */
    public function overlaps($startTime, $endTime, $date): bool
    {
        return $this->appointment_date == $date &&
               $this->start_time < $endTime &&
               $this->end_time > $startTime;
    }
}
