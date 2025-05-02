<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;

class AppointmentAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $userType;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, $userType)
    {
        $this->appointment = $appointment;
        $this->userType = $userType;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'Supervision Meeting Appointment ' . ($this->userType === 'student' ? 'Approved' : 'Confirmation');
        
        return $this->subject($subject)
                    ->markdown('emails.appointments.accepted')
                    ->with([
                        'appointment' => $this->appointment,
                        'userType' => $this->userType,
                        'studentName' => $this->appointment->student->user->name,
                        'lecturerName' => $this->appointment->lecturer->user->name,
                        'date' => $this->appointment->appointment_date,
                        'startTime' => $this->appointment->start_time,
                        'endTime' => $this->appointment->end_time,
                        'meetingType' => $this->appointment->meeting_type,
                        'meetingLink' => $this->appointment->meeting_link,
                    ]);
    }
} 