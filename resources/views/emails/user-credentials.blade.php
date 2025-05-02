@component('mail::message')
# Welcome to FYPMentor System

Dear {{ $credentials['name'] }},

Your account has been created in the FYPMentor System. Here are your login credentials:

**Email:** {{ $credentials['email'] }}
**Temporary Password:** {{ $credentials['password'] }}
**Role:** {{ ucfirst($credentials['role']) }}

Please change your password after your first login.

@component('mail::button', ['url' => route('login')])
Login to System
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
