@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-gray-900">Review Student Proposals</h1>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Filter Proposals</h2>
        <form action="{{ route('lecturer.review-proposals', ['user_id' => auth()->id()]) }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search by Title or Student Name</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2"
                    placeholder="E.g., 'Game Development' or 'John Doe'">
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-4 rounded-md transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
        @forelse($proposals as $proposal)
            @if($proposal->proposal_type === 'student')
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-bold text-gray-900 leading-tight">{{ $proposal->title }}</h3>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full
                                @if($proposal->status === 'pending')
                                    bg-yellow-100 text-yellow-800
                                @elseif($proposal->status === 'approved')
                                    bg-green-100 text-green-800
                                @elseif($proposal->status === 'rejected')
                                    bg-red-100 text-red-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($proposal->status) }}
                            </span>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-user-graduate mr-2 text-gray-500"></i>Proposed by: <span class="font-medium text-gray-700">{{ $proposal->student->user->name }}</span>
                        </p>
                        <p class="text-sm text-gray-500 mb-4">
                            <i class="fas fa-id-card mr-2 text-gray-500"></i>Matric Number: <span class="font-medium text-gray-700">{{ $proposal->student->matric_id }}</span>
                        </p>
                        
                        <div class="bg-gray-50 p-4 rounded-lg mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Project Overview:</h4>
                            <p class="text-gray-700 text-justify mb-2">{{ $proposal->description }}</p>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-tag mr-2 text-gray-500"></i>Field of Study: <span class="font-medium text-gray-700">{{ $proposal->field_of_study }}</span>
                            </p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Contact Information:</h4>
                            <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                                <div><i class="fas fa-envelope mr-2 text-gray-500"></i>Email: <span class="font-medium text-gray-700">{{ $proposal->student->email }}</span></div>
                                <div><i class="fas fa-phone mr-2 text-gray-500"></i>Phone: <span class="font-medium text-gray-700">{{ $proposal->student->phone }}</span></div>
                                <div class="col-span-2"><i class="fas fa-graduation-cap mr-2 text-gray-500"></i>Program: <span class="font-medium text-gray-700">{{ $proposal->student->program }}</span></div>
                                <div class="col-span-2"><i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Submitted: <span class="font-medium text-gray-700">{{ $proposal->created_at->format('d M Y, h:i A') }}</span></div>
                            </div>
                        </div>

                        <div class="mt-6">
                            @if($proposal->status === 'pending' || $proposal->status === 'available')
                                <div class="flex space-x-3">
                                    <button onclick="openReviewModal({{ json_encode([
                                        'id' => $proposal->id,
                                        'title' => $proposal->title,
                                        'description' => $proposal->description,
                                        'field_of_study' => $proposal->field_of_study,
                                        'student_id' => $proposal->student_id,
                                        'student_name' => $proposal->student->user->name,
                                        'student_email' => $proposal->student->email,
                                        'student_program' => $proposal->student->program,
                                        'action' => 'approve'
                                    ]) }})"
                                        class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <i class="fas fa-check mr-2"></i>
                                        Accept & Supervise
                                    </button>
                                    <button onclick="openReviewModal({{ json_encode([
                                        'id' => $proposal->id,
                                        'title' => $proposal->title,
                                        'description' => $proposal->description,
                                        'field_of_study' => $proposal->field_of_study,
                                        'student_id' => $proposal->student_id,
                                        'student_name' => $proposal->student->user->name,
                                        'student_email' => $proposal->student->email,
                                        'student_program' => $proposal->student->program,
                                        'action' => 'reject'
                                    ]) }})"
                                        class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <i class="fas fa-times mr-2"></i>
                                        Reject
                                    </button>
                                </div>
                            @elseif($proposal->status === 'rejected')
                                <div class="text-sm text-red-700 p-3 bg-red-50 rounded-lg">
                                    <p class="font-semibold mb-1"><i class="fas fa-ban mr-2"></i>Rejection Reason:</p>
                                    <p>{{ $proposal->rejection_reason }}</p>
                                </div>
                            @elseif($proposal->status === 'approved')
                                <div class="text-sm text-green-700 p-3 bg-green-50 rounded-lg">
                                    <p class="font-semibold mb-1"><i class="fas fa-check-circle mr-2"></i>Proposal Approved!</p>
                                    <p>This proposal has been accepted and is now under your supervision.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="text-center py-10 text-gray-500 text-lg">
                <p><i class="fas fa-inbox mr-2"></i>No student proposals found matching your criteria.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $proposals->links() }}
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-2xl font-bold leading-6 text-gray-900 mb-6">Review Student Proposal</h3>
                    <form action="{{ route('lecturer.review-proposal', ['user_id' => auth()->id()]) }}" method="POST" id="reviewForm">
                        @csrf
                        <input type="hidden" name="proposal_id" id="review_proposal_id">
                        <input type="hidden" name="action" id="review_action">
                        
                        <div class="space-y-6">
                            <!-- Student Information -->
                            <div class="bg-gray-100 p-4 rounded-lg shadow-sm">
                                <h4 class="text-md font-semibold text-gray-800 mb-3"><i class="fas fa-user-graduate mr-2"></i>Student Information</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <p class="text-gray-600">Name:</p>
                                        <p class="font-medium text-gray-900" id="review_student_name"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Email:</p>
                                        <p class="font-medium text-gray-900" id="review_student_email"></p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <p class="text-gray-600">Program:</p>
                                        <p class="font-medium text-gray-900" id="review_student_program"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Project Details -->
                            <div class="bg-gray-100 p-4 rounded-lg shadow-sm">
                                <h4 class="text-md font-semibold text-gray-800 mb-3"><i class="fas fa-lightbulb mr-2"></i>Project Details</h4>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm text-gray-600">Title:</p>
                                        <p class="font-medium text-gray-900" id="review_title"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Field of Study:</p>
                                        <p class="font-medium text-gray-900" id="review_field_of_study"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Description:</p>
                                        <p class="text-gray-800" id="review_description"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Quota Information -->
                            <div class="bg-blue-100 p-4 rounded-lg shadow-sm border border-blue-200">
                                <h4 class="text-md font-semibold text-blue-800 mb-3"><i class="fas fa-users mr-2"></i>Supervision Quota</h4>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <p class="text-blue-700">Current Students:</p>
                                        <p class="font-bold text-blue-900">{{ Auth::user()->lecturer->current_students }} / {{ Auth::user()->lecturer->max_students }}</p>
                                    </div>
                                    <div>
                                        <p class="text-blue-700">Available Slots:</p>
                                        <p class="font-bold {{ Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students ? 'text-red-600' : 'text-green-600' }}">
                                            {{ Auth::user()->lecturer->max_students - Auth::user()->lecturer->current_students }} slots
                                        </p>
                                    </div>
                                </div>
                                @if(Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students)
                                    <div class="mt-3 text-sm text-red-700 bg-red-50 p-2 rounded-md">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Warning: You have reached your maximum supervision quota. Approving this proposal will not be possible unless a current student is removed.
                                    </div>
                                @endif
                            </div>

                            <!-- Rejection Reason -->
                            <div id="rejection_reason_container" class="hidden bg-red-50 p-4 rounded-lg shadow-sm border border-red-200">
                                <label for="rejection_reason" class="block text-sm font-medium text-red-800 mb-2">Rejection Reason <span class="text-red-500">*</span></label>
                                <textarea name="rejection_reason" id="rejection_reason" rows="4"
                                    class="mt-1 block w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 p-2" placeholder="Please provide a detailed reason for rejection..."></textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end space-x-3">
                            <button type="button" onclick="closeReviewModal()"
                                class="inline-flex items-center px-5 py-2.5 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-300 ease-in-out">
                                Cancel
                            </button>
                            <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-blue-700 hover:bg-blue-800 transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600">
                                <i class="fas fa-paper-plane mr-2"></i> Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openReviewModal(data) {
        document.getElementById('review_proposal_id').value = data.id;
        document.getElementById('review_action').value = data.action;
        document.getElementById('review_title').textContent = data.title;
        document.getElementById('review_field_of_study').textContent = data.field_of_study;
        document.getElementById('review_student_name').textContent = data.student_name;
        document.getElementById('review_student_email').textContent = data.student_email;
        document.getElementById('review_student_program').textContent = data.student_program;
        document.getElementById('review_description').textContent = data.description;
        
        const rejectionContainer = document.getElementById('rejection_reason_container');
        if (data.action === 'reject') {
            rejectionContainer.classList.remove('hidden');
            document.getElementById('rejection_reason').required = true;
        } else {
            rejectionContainer.classList.add('hidden');
            document.getElementById('rejection_reason').required = false;
        }
        
        document.getElementById('reviewModal').classList.remove('hidden');
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
        document.getElementById('reviewForm').reset();
    }
</script>
@endpush
@endsection 