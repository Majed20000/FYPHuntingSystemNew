@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">
            <i class="fas fa-file-alt text-blue-600 mr-3"></i>
            Proposal Details
        </h1>
        <a href="{{ route('lecturer.proposals.manage', ['user_id' => Auth::id()]) }}"
            class="inline-flex items-center px-5 py-2.5 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-300 ease-in-out transform hover:scale-105 shadow-md">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Proposals
        </a>
    </div>

    <!-- Proposal Details Card -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 p-8 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
            <!-- Title -->
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Title:</p>
                <h2 class="text-2xl font-bold text-gray-900">{{ $proposal->title }}</h2>
            </div>

            <!-- Lecturer Name -->
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Lecturer:</p>
                <p class="text-lg text-gray-800 font-medium">{{ $proposal->lecturer->user->name }}</p>
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <p class="text-sm font-semibold text-gray-500 mb-1">Description:</p>
                <p class="text-gray-700 leading-relaxed">{{ $proposal->description }}</p>
            </div>

            <!-- Maximum Students -->
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Maximum Students:</p>
                <p class="text-lg text-gray-800 font-medium">{{ $proposal->lecturer->max_students }}</p>
            </div>

            <!-- Status -->
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Status:</p>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                    @if($proposal->status == 'available') bg-blue-100 text-blue-800
                    @elseif($proposal->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($proposal->status == 'approved') bg-green-100 text-green-800
                    @elseif($proposal->status == 'rejected') bg-red-100 text-red-800
                    @elseif($proposal->status == 'unavailable') bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($proposal->status) }}
                </span>
            </div>

            <!-- Timeframe -->
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Timeframe:</p>
                @if ($proposal->timeframe)
                    <p class="text-lg text-gray-800 font-medium">
                        {{ $proposal->timeframe->semester }} ({{ $proposal->timeframe->academic_year }})
                    </p>
                @else
                    <p class="text-lg text-red-500 italic">No timeframe set</p>
                @endif
            </div>

            <!-- Created At -->
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Created At:</p>
                <p class="text-lg text-gray-800 font-medium">{{ $proposal->created_at->format('M d, Y H:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Actions Section -->
    <div class="flex justify-end space-x-4">
        @php
            $activeTimeframe = App\Models\Timeframe::where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where('status', 'active')
                ->first();

            $proposalSubmissionOpen = false;
            if ($activeTimeframe) {
                $proposalSubmissionOpen = now()->lessThan($activeTimeframe->proposal_submission_deadline);
            }
        @endphp

        @if($proposalSubmissionOpen)
            <a href="{{ route('lecturer.proposals.edit', ['user_id' => Auth::id(), 'proposal' => $proposal->id]) }}"
                class="inline-flex items-center px-6 py-3 bg-blue-700 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 transition duration-300 ease-in-out transform hover:scale-105 shadow-lg">
                <i class="fas fa-edit mr-2"></i>
                Edit Proposal
            </a>

            <form action="{{ route('lecturer.proposals.destroy', ['user_id' => Auth::id(), 'proposal' => $proposal->id]) }}"
                method="POST" class="inline-block"
                onsubmit="return confirm('Are you sure you want to delete this proposal? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-red-700 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 transition duration-300 ease-in-out transform hover:scale-105 shadow-lg">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Delete Proposal
                </button>
            </form>
        @else
            <button disabled
                class="inline-flex items-center px-6 py-3 bg-gray-400 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest cursor-not-allowed shadow-sm">
                <i class="fas fa-lock mr-2"></i>
                Submission Closed
            </button>
        @endif
    </div>
</div>

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush
@endsection
