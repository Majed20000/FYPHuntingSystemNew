@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">
            <i class="fas fa-edit text-blue-600 mr-3"></i>
            Edit Proposal
        </h1>
        <a href="{{ route('lecturer.proposals.manage', ['user_id' => Auth::id()]) }}"
            class="inline-flex items-center px-5 py-2.5 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-300 ease-in-out transform hover:scale-105 shadow-md">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Proposals
        </a>
    </div>

    <!-- Alert Messages -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6 shadow-sm" role="alert">
            <span class="block sm:inline"><i class="fas fa-exclamation-triangle mr-2"></i>Please correct the following errors:</span>
            <ul class="mt-2 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit proposal form -->
    <div class="bg-white shadow-lg rounded-xl px-8 pt-6 pb-8 mb-4 border border-gray-200">
        <form action="{{ route('lecturer.proposals.update', ['user_id' => Auth::id(), 'proposal' => $proposal->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- Title -->
            <div class="mb-6">
                <label class="block text-gray-800 text-sm font-semibold mb-2" for="title">
                    <i class="fas fa-heading mr-2 text-blue-500"></i>Title
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $proposal->title) }}"
                    class="appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out"
                    required>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-gray-800 text-sm font-semibold mb-2" for="description">
                    <i class="fas fa-align-left mr-2 text-blue-500"></i>Description
                </label>
                <textarea name="description" id="description" rows="6"
                    class="appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out"
                    required>{{ old('description', $proposal->description) }}</textarea>
            </div>

            <!-- Maximum Students -->
            <div class="mb-6">
                <label class="block text-gray-800 text-sm font-semibold mb-2">
                    <i class="fas fa-users mr-2 text-blue-500"></i>Maximum Students
                </label>
                <p class="text-gray-600 text-base font-medium">{{ Auth::user()->lecturer->max_students }} students</p>
            </div>

            <!-- Status -->
            <div class="mb-8">
                <label class="block text-gray-800 text-sm font-semibold mb-2" for="status">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>Status
                </label>
                <select name="status" id="status"
                    class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-3 px-4 pr-8 rounded-lg leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out"
                    required>
                    <option value="available" {{ old('status', $proposal->status) == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ old('status', $proposal->status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                </div>
            </div>

            <!-- Update Proposal Button --> 
            <div class="flex items-center justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-700 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 transition duration-300 ease-in-out transform hover:scale-105 shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Update Proposal
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush
@endsection
