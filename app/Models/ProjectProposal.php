<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectProposal extends Model
{
    //Specific table name
    protected $table = 'projectproposal';

    //Specific columns
    protected $fillable = [
        'title',             //Title of the proposal
        'description',       //Description of the proposal
        'lecturer_id',       //Lecturer ID
        'student_id',        //Student ID
        'timeframe_id',      //Timeframe ID
        'proposal_type',     //Type of proposal
        'status',            //Status of the proposal
        'rejection_reason'   //Reason for rejection
    ];

    // Add status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_AVAILABLE = 'available';
    const STATUS_UNAVAILABLE = 'unavailable';

    // Define valid status values
    protected static $validStatuses = [
        self::STATUS_PENDING,
        self::STATUS_ACCEPTED,
        self::STATUS_REJECTED,
        self::STATUS_AVAILABLE,
        self::STATUS_UNAVAILABLE
    ];

    // Add a mutator for the status field
    public function setStatusAttribute($value)
    {
        // Check if the status is valid and throw an error if it is not
        if (!in_array($value, self::$validStatuses)) {
            throw new \InvalidArgumentException("Invalid status value: {$value}");
        }
        // Set the status attribute
        $this->attributes['status'] = $value;
    }

    // Define the relationship with the lecturer
    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id', 'id');
    }

    // Define the relationship with the student
    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    // Define the relationship with the applications
    public function applications()
    {
        return null;
    }

    // Define the relationship with the accepted application
    public function getAcceptedApplication()
    {
        return null;
    }

    // Define the relationship with the timeframe
    public function timeframe()
    {
        return $this->belongsTo(\App\Models\Timeframe::class);
    }

    // Check if proposal is available
    public function isAvailable()
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    // Check if proposal can be applied to
    public function canBeAppliedTo()
    {
        return $this->isAvailable() && !$this->student_id;
    }
}
