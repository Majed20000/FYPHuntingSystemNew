@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">View Proposal</h1>
        <a href="{{ route('lecturer.proposals.manage', ['user_id' => Auth::id()]) }}"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Proposals
        </a>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <!-- Title -->
        <div class="mb-6">
            <h2 class="text-gray-700 text-sm font-bold mb-2">Title</h2>
            <p class="text-gray-900">{{ $proposal->title }}</p>
        </div>

        <!-- Description --> 
        <div class="mb-6">
            <h2 class="text-gray-700 text-sm font-bold mb-2">Description</h2>
            <p class="text-gray-900">{{ $proposal->description }}</p>
        </div>

        <!-- Maximum Students -->
        {{-- <div class="mb-6">
            <h2 class="text-gray-700 text-sm font-bold mb-2">Maximum Students</h2>
            <p class="text-gray-900">{{ $proposal->lecturer->max_students }} students</p>
        </div> --}}

        <!-- Timeframe -->
        <div class="mb-6">
            <h2 class="text-gray-700 text-sm font-bold mb-2">Timeframe</h2>
            @if($proposal->timeframe)
                <p class="text-gray-900">{{ $proposal->timeframe->semester }} {{ $proposal->timeframe->academic_year }}</p>
            @else
                <p class="text-red-500">No timeframe set</p>
            @endif
        </div>

        <!-- Status -->
        <div class="mb-6">
            <h2 class="text-gray-700 text-sm font-bold mb-2">Status</h2>
            <span class="bg-{{ $proposal->status == 'available' ? 'green' : 'red' }}-200 text-{{ $proposal->status == 'available' ? 'green' : 'red' }}-600 py-1 px-3 rounded-full text-xs">
                {{ ucfirst($proposal->status) }}
            </span>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('lecturer.proposals.edit', ['user_id' => Auth::id(), 'proposal' => $proposal->id]) }}"
                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                Edit Proposal
            </a>
            <form action="{{ route('lecturer.proposals.destroy', ['user_id' => Auth::id(), 'proposal' => $proposal->id]) }}"
                method="POST"
                class="inline"
                onsubmit="return confirm('Are you sure you want to delete this proposal?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Delete Proposal
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
