@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">
            <i class="fas fa-file-invoice text-blue-600 mr-3"></i>
            Application Details
        </h1>
        <a href="{{ route('lecturer.manage-applications', ['user_id' => Auth::id()]) }}"
            class="inline-flex items-center px-5 py-2.5 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-300 ease-in-out transform hover:scale-105 shadow-md">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Applications
        </a>
    </div>

    <!-- Application Details Card -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 p-8 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
            <!-- Student Information -->
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Student Name:</p>
                <h2 class="text-2xl font-bold text-gray-900">{{ $application->student->user->name }}</h2>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Student Email:</p>
                <p class="text-lg text-gray-800 font-medium">{{ $application->student->user->email }}</p>
            </div>

            <!-- Proposal Information -->
            <div class="md:col-span-2">
                <p class="text-sm font-semibold text-gray-500 mb-1">Proposal Title:</p>
                <p class="text-lg text-gray-800 font-medium">{{ $application->title }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm font-semibold text-gray-500 mb-1">Proposal Description:</p>
                <p class="text-gray-700 leading-relaxed">{{ $application->description }}</p>
            </div>

            <!-- Application Status -->
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Application Status:</p>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                    @if($application->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($application->status == 'approved') bg-green-100 text-green-800
                    @elseif($application->status == 'rejected') bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($application->status) }}
                </span>
            </div>

            <!-- Applied At -->
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Applied At:</p>
                <p class="text-lg text-gray-800 font-medium">{{ $application->created_at->format('M d, Y H:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Review Actions -->
    @if($application->status == 'pending')
        <div class="flex justify-end space-x-4">
            <button type="button" onclick="openReviewModal('{{ $application->id }}', 'approve')"
                class="inline-flex items-center px-6 py-3 bg-green-700 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600 transition duration-300 ease-in-out transform hover:scale-105 shadow-lg">
                <i class="fas fa-check-circle mr-2"></i>
                Approve
            </button>

            <button type="button" onclick="openReviewModal('{{ $application->id }}', 'reject')"
                class="inline-flex items-center px-6 py-3 bg-red-700 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 transition duration-300 ease-in-out transform hover:scale-105 shadow-lg">
                <i class="fas fa-times-circle mr-2"></i>
                Reject
            </button>
        </div>
    @endif
</div>

<!-- Review Proposal Modal (re-used from reviewProposals.blade.php) -->
<div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
    <div class="relative p-8 bg-white w-full max-w-md m-auto flex-col flex rounded-lg shadow-xl">
        <div class="text-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-2" id="modalTitle"></h3>
            <p class="text-gray-600" id="modalMessage"></p>
        </div>
        <form id="reviewForm" method="POST" action="{{ route('lecturer.review-proposal', ['user_id' => Auth::id()]) }}">
            @csrf
            <input type="hidden" name="application_id" id="application_id">
            <input type="hidden" name="action" id="action">

            <div class="mb-6" id="feedbackSection">
                <label for="feedback" class="block text-gray-700 text-sm font-bold mb-2">Feedback (Optional):</label>
                <textarea name="feedback" id="feedback" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeReviewModal()"
                    class="inline-flex items-center px-5 py-2.5 bg-gray-300 border border-transparent rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-300 ease-in-out shadow-sm">
                    Cancel
                </button>
                <button type="submit" id="confirmReviewBtn"
                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 ease-in-out shadow-sm">
                    Confirm
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script>
    function openReviewModal(applicationId, action) {
        document.getElementById('application_id').value = applicationId;
        document.getElementById('action').value = action;
        document.getElementById('feedbackSection').style.display = 'block'; // Always show feedback

        if (action === 'approve') {
            document.getElementById('modalTitle').innerText = 'Approve Application';
            document.getElementById('modalMessage').innerText = 'Are you sure you want to approve this application? The student will be assigned to your supervision.';
            document.getElementById('confirmReviewBtn').classList.remove('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500');
            document.getElementById('confirmReviewBtn').classList.add('bg-green-600', 'hover:bg-green-700', 'focus:ring-green-500');
            document.getElementById('confirmReviewBtn').innerText = 'Approve';
        } else if (action === 'reject') {
            document.getElementById('modalTitle').innerText = 'Reject Application';
            document.getElementById('modalMessage').innerText = 'Are you sure you want to reject this application? You can provide feedback below.';
            document.getElementById('confirmReviewBtn').classList.remove('bg-green-600', 'hover:bg-green-700', 'focus:ring-green-500');
            document.getElementById('confirmReviewBtn').classList.add('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500');
            document.getElementById('confirmReviewBtn').innerText = 'Reject';
        }

        document.getElementById('reviewModal').classList.remove('hidden');
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
        document.getElementById('feedback').value = ''; // Clear feedback when closing
    }

    // Close modal if clicked outside
    window.onclick = function(event) {
        const modal = document.getElementById('reviewModal');
        if (event.target == modal) {
            closeReviewModal();
        }
    }
</script>
@endpush
@endsection 