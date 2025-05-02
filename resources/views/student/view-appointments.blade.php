@extends('layouts.dashboard')

@section('title', 'My Appointments')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-blue-600">My Appointments</h1>
                    <p class="text-gray-600 mt-1">View all your appointment requests and their status</p>
                </div>
                <a href="{{ route('student.dashboard', ['user_id' => $user_id]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>

            <!-- Tabs -->
            <div x-data="{ activeTab: 'pending' }" class="mb-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button @click="activeTab = 'pending'"
                                :class="{'border-blue-500 text-blue-600': activeTab === 'pending'}"
                                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Pending
                            @if(isset($appointments['pending']))
                                <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                    {{ $appointments['pending']->count() }}
                                </span>
                            @endif
                        </button>

                        <button @click="activeTab = 'approved'"
                                :class="{'border-blue-500 text-blue-600': activeTab === 'approved'}"
                                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Approved
                            @if(isset($appointments['approved']))
                                <span class="ml-2 bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                    {{ $appointments['approved']->count() }}
                                </span>
                            @endif
                        </button>

                        <button @click="activeTab = 'rejected'"
                                :class="{'border-blue-500 text-blue-600': activeTab === 'rejected'}"
                                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Rejected
                            @if(isset($appointments['rejected']))
                                <span class="ml-2 bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                    {{ $appointments['rejected']->count() }}
                                </span>
                            @endif
                        </button>
                    </nav>
                </div>

                <!-- Appointment Lists -->
                <div class="mt-6">
                    <!-- Pending Appointments -->
                    <div x-show="activeTab === 'pending'" class="space-y-4">
                        @forelse($appointments['pending'] ?? [] as $appointment)
                            <div class="bg-white border border-yellow-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $appointment->title }}</h3>
                                        <p class="text-gray-600">{{ $appointment->description }}</p>
                                        <div class="mt-2 space-y-2">
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-user mr-2 text-gray-400"></i>
                                                Lecturer: {{ $appointment->lecturer->user->name }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                                Date: {{ Carbon\Carbon::parse($appointment->appointment_date)->format('l, d F Y') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-clock mr-2 text-gray-400"></i>
                                                Time: {{ Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - 
                                                      {{ Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas {{ $appointment->meeting_type === 'online' ? 'fa-video' : 'fa-building' }} mr-2 text-gray-400"></i>
                                                Meeting Type: {{ ucfirst($appointment->meeting_type) }}
                                            </p>
                                            @if($appointment->meeting_type === 'online' && $appointment->meeting_link)
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-link mr-2 text-gray-400"></i>
                                                    Meeting Link: 
                                                    <a href="{{ $appointment->meeting_link }}" target="_blank" class="text-blue-600 hover:underline">
                                                        {{ $appointment->meeting_link }}
                                                    </a>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <i class="fas fa-inbox text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-500">No pending appointments</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Approved Appointments -->
                    <div x-show="activeTab === 'approved'" class="space-y-4">
                        @forelse($appointments['approved'] ?? [] as $appointment)
                            <div class="bg-white border border-green-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $appointment->title }}</h3>
                                        <p class="text-gray-600">{{ $appointment->description }}</p>
                                        <div class="mt-2 space-y-2">
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-user mr-2 text-gray-400"></i>
                                                Lecturer: {{ $appointment->lecturer->user->name }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                                Date: {{ Carbon\Carbon::parse($appointment->appointment_date)->format('l, d F Y') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-clock mr-2 text-gray-400"></i>
                                                Time: {{ Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - 
                                                      {{ Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas {{ $appointment->meeting_type === 'online' ? 'fa-video' : 'fa-building' }} mr-2 text-gray-400"></i>
                                                Meeting Type: {{ ucfirst($appointment->meeting_type) }}
                                            </p>
                                            @if($appointment->meeting_type === 'online' && $appointment->meeting_link)
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-link mr-2 text-gray-400"></i>
                                                    Meeting Link: 
                                                    <a href="{{ $appointment->meeting_link }}" target="_blank" class="text-blue-600 hover:underline">
                                                        {{ $appointment->meeting_link }}
                                                    </a>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <i class="fas fa-check-circle text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-500">No approved appointments</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Rejected Appointments -->
                    <div x-show="activeTab === 'rejected'" class="space-y-4">
                        @forelse($appointments['rejected'] ?? [] as $appointment)
                            <div class="bg-white border border-red-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $appointment->title }}</h3>
                                        <p class="text-gray-600">{{ $appointment->description }}</p>
                                        <div class="mt-2 space-y-2">
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-user mr-2 text-gray-400"></i>
                                                Lecturer: {{ $appointment->lecturer->user->name }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                                Date: {{ Carbon\Carbon::parse($appointment->appointment_date)->format('l, d F Y') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-clock mr-2 text-gray-400"></i>
                                                Time: {{ Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - 
                                                      {{ Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas {{ $appointment->meeting_type === 'online' ? 'fa-video' : 'fa-building' }} mr-2 text-gray-400"></i>
                                                Meeting Type: {{ ucfirst($appointment->meeting_type) }}
                                            </p>
                                            @if($appointment->meeting_type === 'online' && $appointment->meeting_link)
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-link mr-2 text-gray-400"></i>
                                                    Meeting Link: 
                                                    <a href="{{ $appointment->meeting_link }}" target="_blank" class="text-blue-600 hover:underline">
                                                        {{ $appointment->meeting_link }}
                                                    </a>
                                                </p>
                                            @endif
                                        </div>
                                        @if($appointment->rejection_reason)
                                            <div class="mt-3 pt-3 border-t border-red-100">
                                                <p class="text-sm text-red-600">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    Rejection Reason: {{ $appointment->rejection_reason }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Rejected
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <i class="fas fa-times-circle text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-500">No rejected appointments</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush 