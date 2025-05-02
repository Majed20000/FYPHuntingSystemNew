@extends('layouts.dashboard')

@section('title', 'Student Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Add the countdown component -->
        @include('components.timeframe-countdown')

        <!-- Rest of your dashboard content -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold">Student Dashboard</h1>
                        <p class="text-gray-600 mt-1">Welcome, {{ Auth::user()->name }}!</p>
                    </div>
                    <a href="{{ route('student.view-slots', ['user_id' => auth()->id()]) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        View Available Slots
                    </a>
                </div>

                <!-- Proposal Status Notifications -->
                <div class="mb-8 space-y-4">
                    @php
                        $approvedProposals = Auth::user()->student->projectProposals()->where('status', 'approved')->get();
                        $rejectedProposals = Auth::user()->student->projectProposals()->where('status', 'rejected')->get();
                    @endphp

                    @if($approvedProposals->count() > 0)
                        @foreach($approvedProposals as $proposal)
                            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-green-800">Proposal Approved</h3>
                                        <div class="mt-2 text-sm text-green-700">
                                            <p>Your proposal "{{ $proposal->title }}" has been approved by {{ optional($proposal->lecturer)->name ?? 'your supervisor' }}.</p>
                                        </div>
                                        <div class="mt-2">
                                            <a href="{{ route('student.my-applications', ['user_id' => Auth::id()]) }}" 
                                               class="text-sm font-medium text-green-800 hover:text-green-600">
                                                View Details <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if($rejectedProposals->count() > 0)
                        @foreach($rejectedProposals as $proposal)
                            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-times-circle text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Proposal Rejected</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p>Your proposal "{{ $proposal->title }}" was not approved.</p>
                                            @if($proposal->rejection_reason)
                                                <p class="mt-1"><strong>Reason:</strong> {{ $proposal->rejection_reason }}</p>
                                            @endif
                                        </div>
                                        <div class="mt-2">
                                            <a href="{{ route('student.browse-proposals', ['user_id' => Auth::id()]) }}" 
                                               class="text-sm font-medium text-red-800 hover:text-red-600">
                                                Browse Other Proposals <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Upcoming Appointments Section -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">
                            <i class="fas fa-calendar-check text-blue-500 mr-2"></i>
                            Upcoming Appointments
                        </h2>
                        <a href="{{ route('student.appointments.view', ['user_id' => Auth::id()]) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    @php
                        $upcomingAppointments = Auth::user()->student->appointments()
                            ->where('status', 'approved')
                            ->where('appointment_date', '>=', now())
                            ->orderBy('appointment_date', 'asc')
                            ->orderBy('start_time', 'asc')
                            ->take(3)
                            ->get();
                    @endphp

                    @if($upcomingAppointments->count() > 0)
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($upcomingAppointments as $appointment)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                                    <div class="p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    @if($appointment->meeting_type === 'online')
                                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                            <i class="fas fa-video mr-1"></i>Online
                                                        </span>
                                                    @else
                                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                            <i class="fas fa-user-group mr-1"></i>In-person
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }} - 
                                                    {{ \Carbon\Carbon::parse($appointment->end_time)->format('g:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h3 class="text-sm font-medium text-gray-900">{{ $appointment->title }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">
                                                With: {{ optional($appointment->lecturer->user)->name }}
                                            </p>
                                        </div>

                                        @if($appointment->meeting_type === 'online' && $appointment->meeting_link)
                                            <a href="{{ $appointment->meeting_link }}" target="_blank" 
                                               class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-video mr-2"></i>
                                                Join Meeting
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-calendar-plus text-gray-400 text-4xl mb-3"></i>
                                <h3 class="text-gray-900 font-medium">No Upcoming Appointments</h3>
                                <p class="text-gray-500 mt-1">Schedule a meeting with your supervisor</p>
                                <a href="{{ route('student.view-slots', ['user_id' => Auth::id()]) }}" 
                                   class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Book Appointment
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush