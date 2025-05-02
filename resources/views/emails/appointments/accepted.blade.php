@component('mail::message')
# Supervision Meeting Appointment {{ $userType === 'student' ? 'Approved' : 'Confirmation' }}

Dear {{ $userType === 'student' ? $studentName : $lecturerName }},

@if($userType === 'student')
Your supervision meeting appointment has been approved by {{ $lecturerName }}.
@else
This is a confirmation of the supervision meeting appointment with {{ $studentName }}.
@endif

**Meeting Details:**
- **Date:** {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
- **Time:** {{ \Carbon\Carbon::parse($startTime)->format('g:i A') }} - {{ \Carbon\Carbon::parse($endTime)->format('g:i A') }}
- **Meeting Type:** {{ ucfirst($meetingType) }}
@if($meetingType === 'online' && $meetingLink)
- **Meeting Link:** [Join Meeting]({{ $meetingLink }})
@endif

@if($userType === 'student')
Please make sure to:
1. Be punctual for the meeting
2. Prepare any necessary materials or questions
3. Follow up with your supervisor after the meeting
@else
The student has been notified of the approval.
@endif

@component('mail::button', ['url' => $meetingType === 'online' ? $meetingLink : '#'])
{{ $meetingType === 'online' ? 'Join Meeting' : 'View Details' }}
@endcomponent

Best regards,  
{{ config('app.name') }}
@endcomponent 