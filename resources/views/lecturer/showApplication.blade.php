@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Application Details</h1>
        <a href="{{ route('lecturer.applications.manage', ['user_id' => Auth::id()]) }}"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Applications
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Student Information Section -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Student Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600">Name</p>
                    <p class="text-lg font-medium">{{ optional($application->student)->user->name ?? 'Unknown Student' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="text-lg font-medium">{{ optional($application->student)->user->email ?? 'No email available' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Student ID</p>
                    <p class="text-lg font-medium">{{ optional($application->student)->matric_id ?? 'No ID available' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Applied Date</p>
                    <p class="text-lg font-medium">{{ $application->created_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Proposal Details Section -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Proposal Details</h2>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Title</p>
                    <p class="text-lg font-medium">{{ $application->title }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Description</p>
                    <p class="text-lg">{{ $application->description }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($application->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($application->status == 'approved') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($application->status) }}
                    </span>
                </div>
                @if($application->status == 'rejected' && $application->rejection_reason)
                    <div>
                        <p class="text-sm text-gray-600">Rejection Reason</p>
                        <p class="text-lg text-red-600">{{ $application->rejection_reason }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Timeframe Information -->
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Timeframe Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600">Semester</p>
                    <p class="text-lg font-medium">{{ optional($application->timeframe)->semester ?? 'Not specified' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Academic Year</p>
                    <p class="text-lg font-medium">{{ optional($application->timeframe)->academic_year ?? 'Not specified' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 