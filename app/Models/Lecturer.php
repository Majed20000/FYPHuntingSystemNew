<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    protected $table = 'lecturer';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'name',
        'staff_id',
        'email',
        'phone',
        'research_group',
        'max_students',
        'current_students',
        'accepting_students'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'lecturer_id', 'user_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'supervise', 'lecturer_id', 'student_id', 'id', 'id')
            ->select(['student.id', 'student.name', 'student.matric_id', 'student.email'])
            ->withTimestamps();
    }
}
