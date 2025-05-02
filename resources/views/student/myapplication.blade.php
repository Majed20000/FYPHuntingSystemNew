@extends('layouts.dashboard')

@section('title', 'My Applications')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <h1 class="text-2xl font-bold mb-6">My Applications</h1>

    <div class="space-y-6">
        @forelse($applications as $application)


            <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow duration-200
                @if($application->status === 'approved') border-green-200 bg-green-50
                @elseif($application->status === 'rejected') border-red-200 bg-red-50
                @else border-gray-200 bg-gray-50
                @endif">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $application->title }}</h3>
                        <p class="text-sm text-gray-600">
                            @php
                                $lecturer = $application->lecturer ?? $lecturers->firstWhere('id', $application->lecturer_id);
                            @endphp
                            Supervisor: {{ $lecturer ? $lecturer->name : 'Unknown Lecturer' }}
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
                        {{ ucfirst($application->status) }}
                    </span>
                </div>

                <div class="mt-3">
                    <p class="text-gray-600">{{ $application->description }}</p>
                </div>

                @if($application->status === 'rejected' && $application->rejection_reason)
                    <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-md">
                        <h4 class="text-sm font-medium text-red-800">Rejection Reason:</h4>
                        <p class="text-sm text-red-600">{{ $application->rejection_reason }}</p>
                    </div>
                @endif

                <div class="mt-4 text-sm text-gray-500">
                    <span class="mr-4">
                        <i class="fas fa-clock mr-1"></i>
                        Submitted: {{ $application->created_at ? $application->created_at->diffForHumans() : 'N/A' }}
                    </span>
                    @if($application->updated_at && $application->updated_at->ne($application->created_at))
                        <span>
                            <i class="fas fa-history mr-1"></i>
                            Last Updated: {{ $application->updated_at->diffForHumans() }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                <p>You haven't submitted any applications yet.</p>
                <a href="{{ route('student.browse-proposals', ['user_id' => auth()->id()]) }}"
                   class="inline-block mt-4 text-blue-600 hover:text-blue-800">
                    Browse Available Proposals
                </a>
            </div>
        @endforelse

        <!-- Pagination -->
        <div class="mt-6">
            {{ $applications->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush
