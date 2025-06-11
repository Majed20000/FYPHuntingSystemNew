@extends('layouts.dashboard')

@php
use App\Models\ProjectProposal;
@endphp

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manage Applications</h1>

        <!-- Filters -->
        <div class="flex space-x-4">
            <form action="{{ route('lecturer.applications.manage', ['user_id' => Auth::id()]) }}" method="GET" class="flex space-x-4">
                <select name="status" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>

                <div class="flex">
                    <input type="text" name="proposal" placeholder="Search by proposal title" value="{{ request('proposal') }}"
                           class="rounded-l-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-r-md hover:bg-blue-600">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif



    <!-- Applications List -->
    <div class="bg-white shadow-md rounded my-6">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Student</th>
                    <th class="py-3 px-6 text-left">Proposal Title</th>
                    <th class="py-3 px-6 text-left">Description</th>
                    <th class="py-3 px-6 text-center">Status</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @forelse($applications as $application)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">
                            {{ optional($application->student)->user->name ?? 'Unknown Student' }}
                        </td>
                        <td class="py-3 px-6 text-left">{{ $application->title }}</td>
                        <td class="py-3 px-6 text-left">{{ Str::limit($application->description, 100) }}</td>
                        <td class="py-3 px-6 text-center">
                            <span class="bg-{{ $application->status == 'pending' ? 'yellow' : ($application->status == 'approved' ? 'green' : 'red') }}-200
                                       text-{{ $application->status == 'pending' ? 'yellow' : ($application->status == 'approved' ? 'green' : 'red') }}-600
                                       py-1 px-3 rounded-full text-xs">
                                {{ ucfirst($application->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-6 text-center">
                            @if($application->status == 'pending')
                                <div class="flex justify-center space-x-2">
                                    <button onclick="openReviewModal({{ $application->id }}, '{{ optional($application->student)->user->name }}', '{{ optional($application->student)->user->email }}', '{{ $application->title }}', '{{ $application->description }}', '{{ $application->created_at->format('d M Y, h:i A') }}')"
                                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                        Review
                                    </button>
                                    <a href="{{ route('lecturer.applications.show', ['user_id' => Auth::id(), 'application' => $application->id]) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-info-circle"></i> Details
                                    </a>
                                </div>
                            @else
                                <div class="flex justify-center space-x-2">
                                    <span class="text-gray-500">{{ $application->rejection_reason ?? 'No remarks' }}</span>
                                    <a href="{{ route('lecturer.applications.show', ['user_id' => Auth::id(), 'application' => $application->id]) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-info-circle"></i> Details
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500">
                            No applications found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4">
            {{ $applications->links() }}
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Review Application</h2>
                    </div>

                    <!-- Student Details -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Student Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Name:</p>
                                <p class="font-medium" id="studentName"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Email:</p>
                                <p class="font-medium" id="studentEmail"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Applied:</p>
                                <p class="font-medium" id="appliedDate"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Proposal Details -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Proposal Details</h3>
                        <div>
                            <p class="text-sm text-gray-600">Title:</p>
                            <p class="font-medium" id="proposalTitle"></p>
                        </div>
                    </div>

                    <!-- Student's Remarks -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Student's Remarks</h3>
                        <p class="text-gray-700" id="proposalDescription"></p>
                    </div>

                    <!-- Review Form -->
                    <form id="reviewForm" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Quota Information -->
                        <div class="mb-6 p-4 rounded-lg {{ Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200' }}">
                            <h3 class="text-lg font-semibold mb-2 {{ Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students ? 'text-red-700' : 'text-green-700' }}">
                                Supervision Quota Status
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Current Students:</p>
                                    <p class="font-medium">{{ Auth::user()->lecturer->current_students }} / {{ Auth::user()->lecturer->max_students }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Available Slots:</p>
                                    <p class="font-medium {{ Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students ? 'text-red-600' : 'text-green-600' }}">
                                        {{ Auth::user()->lecturer->max_students - Auth::user()->lecturer->current_students }} slots
                                    </p>
                                </div>
                            </div>
                            @if(Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students)
                                <div class="mt-2 text-sm text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Warning: You have reached your maximum supervision quota. You cannot approve more applications at this time.
                                </div>
                            @endif
                        </div>

                        <!-- Decision -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Decision
                            </label>
                            <div class="space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="approved" class="form-radio" required
                                           {{ Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students ? 'disabled' : '' }}>
                                    <span class="ml-2 {{ Auth::user()->lecturer->current_students >= Auth::user()->lecturer->max_students ? 'text-gray-400' : '' }}">
                                        Accept Application
                                    </span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="rejected" class="form-radio" required>
                                    <span class="ml-2">Reject Application</span>
                                </label>
                            </div>
                        </div>

                        <!-- Rejection Reason -->
                        <div id="rejectionReasonDiv" class="hidden">
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                Rejection Reason <span class="text-red-500">*</span>
                            </label>
                            <textarea id="rejection_reason" name="rejection_reason" rows="4"
                                class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Please provide a reason for rejection..."></textarea>
                        </div>
                    </form>
                </div>

                <!-- Submit Review Button -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="submitReview()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Submit Review
                    </button>

                    <!-- Cancel Button -->
                    <button type="button" onclick="closeReviewModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Scripts -->
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
}

// Close review modal
function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
    document.getElementById('reviewForm').reset();
    document.getElementById('rejectionReasonDiv').classList.add('hidden');
    window.currentApplication = null;
}

// Create notification
function createNotification(studentId, status, title, message) {
    // Get existing notifications or initialize empty object
    const storedNotifications = JSON.parse(localStorage.getItem('studentNotifications') || '{}');

    // Initialize array for this student if it doesn't exist
    if (!storedNotifications[studentId]) {
        storedNotifications[studentId] = [];
    }

    // Create new notification
    const notification = {
        id: Date.now().toString(),
        title: title,
        message: message,
        type: status === 'approved' ? 'success' : 'info',
        timestamp: new Date().toISOString(),
        read: false
    };

    // Add to beginning of array
    storedNotifications[studentId].unshift(notification);

    // Keep only last 10 notifications
    storedNotifications[studentId] = storedNotifications[studentId].slice(0, 10);

    // Save back to localStorage
    localStorage.setItem('studentNotifications', JSON.stringify(storedNotifications));

    console.log('Notification created:', notification);
}

// Submit review
function submitReview() {
    const form = document.getElementById('reviewForm');
    const status = form.querySelector('input[name="status"]:checked')?.value;
    const rejectionReason = form.querySelector('#rejection_reason');
    const currentStudents = {{ Auth::user()->lecturer->current_students }};
    const maxStudents = {{ Auth::user()->lecturer->max_students }};

    // Check if status is selected
    if (!status) {
        alert('Please select a decision');
        return;
    }

    // Check if maximum students is reached
    if (status === 'approved' && currentStudents >= maxStudents) {
        alert('Cannot approve application. You have reached your maximum supervision quota of ' + maxStudents + ' students.');
        return;
    }

    // Check if rejection reason is provided
    if (status === 'rejected' && !rejectionReason.value.trim()) {
        alert('Please provide a reason for rejection');
        return;
    }

    // Create notification for student
    if (window.currentApplication) {
        const studentId = window.currentApplication.id;
        const title = status === 'approved' ? 'Application Approved' : 'Application Rejected';
        const message = status === 'approved'
            ? `Your application "${window.currentApplication.title}" has been approved.`
            : `Your application "${window.currentApplication.title}" has been rejected. Reason: ${rejectionReason.value}`;

        createNotification(studentId, status, title, message);
    }

    // Debug info before submission
    console.log('Submitting form with:', {
        action: form.action,
        status: status,
        rejectionReason: rejectionReason.value,
        currentStudents: currentStudents,
        maxStudents: maxStudents
    });

    form.submit();
}

// Show/hide rejection reason based on status selection
document.querySelectorAll('input[name="status"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const rejectionDiv = document.getElementById('rejectionReasonDiv');
        const rejectionInput = document.getElementById('rejection_reason');

        if (this.value === 'rejected') {
            rejectionDiv.classList.remove('hidden');
            rejectionInput.required = true;
        } else {
            rejectionDiv.classList.add('hidden');
            rejectionInput.required = false;
            rejectionInput.value = ''; // Clear the rejection reason when not rejected
        }
    });
});
</script>
@endpush
@endsection
