@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Proposal</h1>
        <a href="{{ route('lecturer.proposals.manage', ['user_id' => Auth::id()]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Proposals
        </a>
    </div>

    <!-- Display errors -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit proposal form -->
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <form action="{{ route('lecturer.proposals.update', ['user_id' => Auth::id(), 'proposal' => $proposal->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- Title -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                    Title
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $proposal->title) }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                    Description
                </label>
                <textarea name="description" id="description" rows="4"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>{{ old('description', $proposal->description) }}</textarea>
            </div>

            <!-- Maximum Students -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Maximum Students
                </label>
                <p class="text-gray-600">{{ Auth::user()->lecturer->max_student }} students</p>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                    Status
                </label>
                <select name="status" id="status"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                    <option value="available" {{ old('status', $proposal->status) == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ old('status', $proposal->status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
            </div>

            <!-- Update Proposal Button --> 
            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Proposal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
