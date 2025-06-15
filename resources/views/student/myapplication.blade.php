@extends('layouts.dashboard')

@section('title', 'My Applications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">
            <i class="fas fa-file-alt text-blue-600 mr-3"></i>
            My Applications
        </h1>
    </div>

    <!-- Applications List -->
    <div class="space-y-6">
        @forelse($applications as $application)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border
                @if($application->status === 'approved') border-green-200
                @elseif($application->status === 'rejected') border-red-200
                @else border-gray-200
                @endif p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $application->title }}</h3>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-user-tie mr-2 text-gray-500"></i>Supervisor:
                            @php
                                $lecturer = $application->lecturer ?? $lecturers->firstWhere('id', $application->lecturer_id);
                            @endphp
                            <span class="font-medium text-gray-800">{{ $lecturer ? $lecturer->name : 'Unknown Lecturer' }}</span>
                            @if($lecturer)
                                <span class="text-gray-500">({{ $lecturer->staff_id }})</span>
                            @endif
                        </p>
                    </div>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                        @if($application->status === 'approved') bg-green-100 text-green-800
                        @elseif($application->status === 'rejected') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        <i class="fas fa-info-circle mr-1"></i>{{ ucfirst($application->status) }}
                    </span>
                </div>

                <p class="text-gray-700 leading-relaxed mb-4">{{ $application->description }}</p>

                @if($application->status === 'rejected' && $application->rejection_reason)
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <h4 class="text-base font-semibold text-red-800 mb-2"><i class="fas fa-times-circle mr-2"></i>Rejection Reason:</h4>
                        <p class="text-sm text-red-700">{{ $application->rejection_reason }}</p>
                    </div>
                @endif

                <div class="mt-4 text-sm text-gray-500 flex items-center space-x-4">
                    <span>
                        <i class="fas fa-clock mr-1 text-gray-400"></i>
                        Submitted: <span class="font-medium text-gray-700">{{ $application->created_at ? $application->created_at->diffForHumans() : 'N/A' }}</span>
                    </span>
                    @if($application->updated_at && $application->updated_at->ne($application->created_at))
                        <span>
                            <i class="fas fa-history mr-1 text-gray-400"></i>
                            Last Updated: <span class="font-medium text-gray-700">{{ $application->updated_at->diffForHumans() }}</span>
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-10 bg-white rounded-xl shadow-lg border border-gray-200">
                <i class="fas fa-frown text-gray-400 text-6xl mb-4"></i>
                <p class="text-xl font-semibold text-gray-700 mb-2">You haven't submitted any applications yet.</p>
                <p class="text-gray-500">Start exploring proposals today!</p>
                <a href="{{ route('student.browse-lecturers', ['user_id' => auth()->id()]) }}"
                   class="inline-flex items-center mt-6 px-6 py-3 bg-blue-700 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 transition duration-300 ease-in-out transform hover:scale-105 shadow-lg">
                    <i class="fas fa-search mr-2"></i>
                    Browse Lecturers
                </a>
            </div>
        @endforelse

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            {{ $applications->links() }}
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush
@endsection
