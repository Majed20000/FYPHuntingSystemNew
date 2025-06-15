@extends('layouts.dashboard')

@php
use App\Models\ProjectProposal;
@endphp

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">Manage Applications</h1>

        <!-- Action Buttons -->
        <div class="flex space-x-3">
            <a href="{{ route('lecturer.review-proposals', ['user_id' => Auth::id()]) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-700 hover:bg-blue-800 transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 shadow-md">
                <i class="fas fa-file-alt mr-2"></i>
                Review Student Proposals
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Filter Applications</h2>
        <form action="{{ route('lecturer.applications.manage', ['user_id' => Auth::id()]) }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                <select name="status" id="status" onchange="this.form.submit()"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="proposal" class="block text-sm font-medium text-gray-700 mb-1">Search by Proposal Title</label>
                <div class="flex">
                    <input type="text" name="proposal" id="proposal" placeholder="E.g., 'Smart City Project'" value="{{ request('proposal') }}"
                           class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2">
                    <button type="submit" class="px-6 py-2.5 bg-blue-700 text-white rounded-r-md hover:bg-blue-800 transition duration-300 ease-in-out transform hover:scale-105">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6 shadow-sm" role="alert">
            <span class="block sm:inline"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6 shadow-sm" role="alert">
            <span class="block sm:inline"><i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Applications List -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden mb-6 border border-gray-200">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-100 text-gray-700 uppercase text-sm font-semibold tracking-wider">
                    <th class="py-3 px-6 text-left">Student</th>
                    <th class="py-3 px-6 text-left">Proposal Title</th>
                    <th class="py-3 px-6 text-left">Description</th>
                    <th class="py-3 px-6 text-center">Status</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($applications as $application)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-150 ease-in-out">
                        <td class="py-4 px-6 text-left whitespace-no-wrap">
                            <div class="flex items-center">
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">{{ optional($application->student)->user->name ?? 'Unknown Student' }}</p>
                                    <p class="text-gray-500">{{ optional($application->student)->matric_id ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-left font-medium text-gray-800">{{ $application->title }}</td>
                        <td class="py-4 px-6 text-left text-gray-600">{{ Str::limit($application->description, 70) }}</td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($application->status == 'pending')
                                    bg-yellow-100 text-yellow-800
                                @elseif($application->status == 'approved')
                                    bg-green-100 text-green-800
                                @else
                                    bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($application->status) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                @if($application->status == 'pending')
                                    <button onclick="openReviewModal({{ $application->id }}, '{{ optional($application->student)->user->name }}', '{{ optional($application->student)->user->email }}', '{{ $application->title }}', '{{ $application->description }}', '{{ $application->created_at->format('d M Y, h:i A') }}')"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                        <i class="fas fa-eye mr-1"></i> Review
                                    </button>
                                @else
                                    <span class="text-gray-500 text-xs italic">{{ $application->rejection_reason ?? 'No remarks' }}</span>
                                @endif
                                <a href="{{ route('lecturer.applications.show', ['user_id' => Auth::id(), 'application' => $application->id]) }}"
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                    <i class="fas fa-info-circle mr-1"></i> Details
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-500 text-lg">
                            <i class="fas fa-folder-open mr-2"></i>No applications found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-center">
            {{ $applications->links() }}
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Review Application</h2>
                    </div>

                    <!-- Student Details -->
                    <div class="mb-6 p-4 bg-gray-100 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3"><i class="fas fa-user-graduate mr-2"></i>Student Details</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-sm text-gray-600">Name:</p>
                                <p class="font-medium text-gray-900" id="studentName"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Email:</p>
                                <p class="font-medium text-gray-900" id="studentEmail"></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm text-gray-600">Applied Date:</p>
                                <p class="font-medium text-gray-900" id="appliedDate"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Proposal Details -->
                    <div class="mb-6 p-4 bg-gray-100 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3"><i class="fas fa-lightbulb mr-2"></i>Proposal Details</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Title:</p>
                                <p class="font-medium text-gray-900" id="proposalTitle"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Description:</p>
                                <p class="text-gray-800" id="proposalDescription"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Review Form -->
                    <form id="reviewForm" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Quota Information -->
                        <div class="mb-6 p-4 rounded-lg shadow-sm {{ Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students ? 'bg-red-100 border border-red-200' : 'bg-green-100 border border-green-200' }}">
                            <h3 class="text-lg font-semibold mb-3 {{ Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students ? 'text-red-800' : 'text-green-800' }}">
                                <i class="fas fa-users mr-2"></i>Supervision Quota Status
                            </h3>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-sm {{ Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students ? 'text-red-700' : 'text-green-700' }}">Current Students:</p>
                                    <p class="font-bold {{ Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students ? 'text-red-900' : 'text-green-900' }}">
                                        {{ Auth::user()->lecturer->current_students }} / {{ Auth::user()->lecturer->max_students }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm {{ Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students ? 'text-red-700' : 'text-green-700' }}">Available Slots:</p>
                                    <p class="font-bold {{ Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students ? 'text-red-900' : 'text-green-900' }}">
                                        {{ Auth::user()->lecturer->max_students - Auth::user()->lecturer->current_students }} slots
                                    </p>
                                </div>
                            </div>
                            @if(Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students)
                                <div class="mt-3 text-sm text-red-700 bg-red-50 p-2 rounded-md border border-red-200">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Warning: You have reached your maximum supervision quota. You cannot approve more applications at this time.
                                </div>
                            @endif
                        </div>

                        <!-- Decision -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Decision
                            </label>
                            <div class="flex items-center space-x-6">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="status" value="approved" class="form-radio text-green-600 focus:ring-green-500"
                                           required {{ Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students ? 'disabled' : '' }}>
                                    <span class="ml-2 text-base font-medium {{ Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students ? 'text-gray-400' : 'text-green-700' }}">
                                        <i class="fas fa-check-circle mr-1"></i> Accept Application
                                    </span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="status" value="rejected" class="form-radio text-red-600 focus:ring-red-500" required>
                                    <span class="ml-2 text-base font-medium text-red-700"><i class="fas fa-times-circle mr-1"></i> Reject Application</span>
                                </label>
                            </div>
                        </div>

                        <!-- Rejection Reason -->
                        <div id="rejectionReasonDiv" class="hidden bg-red-50 p-4 rounded-lg shadow-sm border border-red-200">
                            <label for="rejection_reason" class="block text-sm font-medium text-red-800 mb-2">
                                Rejection Reason <span class="text-red-500">*</span>
                            </label>
                            <textarea id="rejection_reason" name="rejection_reason" rows="4"
                                class="w-full rounded-md shadow-sm border-red-300 focus:border-red-500 focus:ring-red-500 p-2"
                                placeholder="Please provide a detailed reason for rejection..."></textarea>
                        </div>

                        <div class="mt-8 flex justify-end space-x-3">
                            <button type="button" onclick="closeReviewModal()"
                                class="inline-flex items-center px-5 py-2.5 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-300 ease-in-out">
                                Cancel
                            </button>
                            <button type="button" onclick="submitReview()"
                                class="inline-flex items-center px-5 py-2.5 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 transition duration-300 ease-in-out transform hover:scale-105">
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
function openReviewModal(applicationId, studentName, studentEmail, title, description, appliedDate) {
    document.getElementById('reviewModal').classList.remove('hidden');
    document.getElementById('studentName').textContent = studentName;
    document.getElementById('studentEmail').textContent = studentEmail;
    document.getElementById('appliedDate').textContent = appliedDate;
    document.getElementById('proposalTitle').textContent = title;
    document.getElementById('proposalDescription').textContent = description;

    // Store student info for notification
    window.currentApplication = {
        id: applicationId,
        studentName: studentName,
        title: title
    };

    // Set the correct form action URL
    const baseUrl = "{{ route('lecturer.applications.manage', ['user_id' => Auth::id()]) }}";
    const reviewUrl = `${baseUrl.replace('/applications', '')}/applications/${applicationId}/review`;
    document.getElementById('reviewForm').action = reviewUrl;

    // Debug info
    console.log('Form action set to:', reviewUrl);

    // Handle rejection reason visibility
    const statusRadios = document.querySelectorAll('input[name="status"]');
    const rejectionReasonDiv = document.getElementById('rejectionReasonDiv');
    const rejectionReasonTextarea = document.getElementById('rejection_reason');

    statusRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'rejected') {
                rejectionReasonDiv.classList.remove('hidden');
                rejectionReasonTextarea.setAttribute('required', 'required');
            } else {
                rejectionReasonDiv.classList.add('hidden');
                rejectionReasonTextarea.removeAttribute('required');
            }
        });
    });

    // Reset radio buttons and hide rejection reason when modal opens
    statusRadios.forEach(radio => radio.checked = false);
    rejectionReasonDiv.classList.add('hidden');
    rejectionReasonTextarea.removeAttribute('required');
}

// Close review modal
function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
    document.getElementById('reviewForm').reset();
    document.getElementById('rejectionReasonDiv').classList.add('hidden');
    window.currentApplication = null;
}

// Submit review
function submitReview() {
    const reviewForm = document.getElementById('reviewForm');
    const status = document.querySelector('input[name="status"]:checked');

    if (!status) {
        alert('Please select a decision (Accept or Reject).');
        return;
    }

    if (status.value === 'rejected' && !document.getElementById('rejection_reason').value.trim()) {
        alert('Please provide a rejection reason.');
        return;
    }

    reviewForm.submit();
}

// Create notification
function createNotification(applicationId, studentName, title, status) {
    fetch('/notifications', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            application_id: applicationId,
            student_name: studentName,
            title: title,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => console.log('Notification sent:', data))
    .catch(error => console.error('Error sending notification:', error));
}
</script>
@endpush
@endsection
