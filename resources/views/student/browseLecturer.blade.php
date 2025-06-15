@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">
            <i class="fas fa-users-class text-blue-600 mr-3"></i>
            Browse Lecturers
        </h1>
        <a href="{{ route('student.list-lecturer-proposals', ['user_id' => auth()->id()]) }}"
            class="inline-flex items-center px-5 py-2.5 bg-blue-700 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 transition duration-300 ease-in-out transform hover:scale-105 shadow-md">
            <i class="fas fa-scroll mr-2"></i>
            View All Proposals
        </a>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-8 border border-gray-200">
        <form action="{{ route('student.browse-lecturers', ['user_id' => auth()->id()]) }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="search" class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-search mr-2 text-gray-500"></i>Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3"
                    placeholder="Search by name or field">
            </div>
            <div>
                <label for="field" class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-tag mr-2 text-gray-500"></i>Field of Study</label>
                <select name="field" id="field"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3">
                    <option value="">All Fields</option>
                    @foreach ($fields as $field)
                        <option value="{{ $field }}" {{ request('field') == $field ? 'selected' : '' }}>
                            {{ $field }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full inline-flex justify-center items-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 ease-in-out transform hover:scale-105 shadow-md">
                    <i class="fas fa-filter mr-2"></i>
                    Filter Results
                </button>
            </div>
        </form>
    </div>

    <!-- Lecturers List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($lecturers as $lecturer)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $lecturer->name }}</h3>
                            <p class="text-sm text-gray-600"><i class="fas fa-flask mr-2 text-gray-500"></i>{{ $lecturer->research_group ?? 'N/A' }}</p>
                        </div>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                            @if($lecturer->current_students < $lecturer->max_students)
                                bg-green-100 text-green-800
                            @else
                                bg-red-100 text-red-800
                            @endif">
                            <i class="fas fa-user-graduate mr-1"></i>{{ $lecturer->current_students }}/{{ $lecturer->max_students }} Students
                        </span>
                    </div>
                    <p class="text-gray-700 mb-4"><i class="fas fa-envelope mr-2 text-gray-500"></i>{{ $lecturer->email }}</p>
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-id-badge mr-2 text-gray-500"></i>Staff ID: <span class="font-medium text-gray-700">{{ $lecturer->staff_id }}</span>
                        </div>
                        @if ($lecturer->current_students < $lecturer->max_students)
                            <a href="{{ route('student.list-lecturer-proposals', ['user_id' => auth()->id(), 'lecturer_id' => $lecturer->id]) }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent text-sm font-medium rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 ease-in-out transform hover:scale-105 shadow-sm">
                                <i class="fas fa-file-alt mr-2"></i>
                                View Proposals
                            </a>
                        @else
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-700"><i class="fas fa-lock mr-1"></i>Not Accepting</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-10 bg-white rounded-xl shadow-lg border border-gray-200">
                <i class="fas fa-frown text-gray-400 text-6xl mb-4"></i>
                <p class="text-xl font-semibold text-gray-700 mb-2">No lecturers found matching your criteria.</p>
                <p class="text-gray-500">Try adjusting your search or filters.</p>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
        {{ $lecturers->links() }}
    </div>
</div>

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush
@endsection
