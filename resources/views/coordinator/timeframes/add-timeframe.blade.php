@extends('layouts.dashboard')

@section('title', 'Add Timeframe')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Add New Timeframe</h1>
            <p class="text-gray-600 mt-1">Set up a new academic timeframe period</p>
        </div>
        <a href="{{ route('coordinator.timeframes.index') }}"
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Timeframes
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('coordinator.timeframes.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Academic Year and Semester -->
            <div>
                <label for="academic_year" class="block text-sm font-medium text-gray-700">Academic Year</label>
                <input type="text" name="academic_year" id="academic_year"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="e.g., 2025/2026" required pattern="\d{4}/\d{4}"
                       title="Please enter in format: YYYY/YYYY (e.g., 2025/2026)"
                       value="{{ old('academic_year') }}">
                <p class="mt-1 text-sm text-gray-500">Format: YYYY/YYYY (e.g., 2025/2026)</p>
                @error('academic_year')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="semester" class="block text-sm font-medium text-gray-700">Semester</label>
                <select name="semester" id="semester"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>Semester 1</option>
                    <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>Semester 2</option>
                    <option value="3" {{ old('semester') == '3' ? 'selected' : '' }}>Semester 3</option>
                </select>
                @error('semester')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Start and End Dates -->
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" id="start_date"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       required value="{{ old('start_date') }}">
                @error('start_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" name="end_date" id="end_date"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       required value="{{ old('end_date') }}">
                @error('end_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deadlines -->
            <div>
                <label for="proposal_submission_deadline" class="block text-sm font-medium text-gray-700">
                    Proposal Submission Deadline
                </label>
                <input type="datetime-local" name="proposal_submission_deadline" id="proposal_submission_deadline"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       required value="{{ old('proposal_submission_deadline') }}">
                @error('proposal_submission_deadline')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="supervisor_confirmation_deadline" class="block text-sm font-medium text-gray-700">
                    Supervisor Confirmation Deadline
                </label>
                <input type="datetime-local" name="supervisor_confirmation_deadline" id="supervisor_confirmation_deadline"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       required value="{{ old('supervisor_confirmation_deadline') }}">
                @error('supervisor_confirmation_deadline')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Maximum Limits -->
            <div>
                <label for="max_applications_per_student" class="block text-sm font-medium text-gray-700">
                    Max Applications per Student
                </label>
                <input type="number" name="max_applications_per_student" id="max_applications_per_student"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       min="1" max="10" value="{{ old('max_applications_per_student', 1) }}" required>
                @error('max_applications_per_student')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="max_appointments_per_student" class="block text-sm font-medium text-gray-700">
                    Max Appointments per Student
                </label>
                <input type="number" name="max_appointments_per_student" id="max_appointments_per_student"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       min="1" max="10" value="{{ old('max_appointments_per_student', 3) }}" required>
                @error('max_appointments_per_student')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Status and Active State -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                       {{ old('is_active') ? 'checked' : '' }}>
                <label for="is_active" class="ml-2 block text-sm text-gray-700 flex items-center">
                    Set as Active Period
                    <i class="fas fa-info-circle ml-1 text-gray-400 hover:text-gray-600 cursor-help"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Select this to make the current timeframe the active one for student assignments."></i>
                </label>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <button type="button" onclick="window.location.href='{{ route('coordinator.timeframes.index') }}'"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </button>
            <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Create Timeframe
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Validate end date is after start date
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    function validateDates() {
        if (startDate.value && endDate.value && endDate.value < startDate.value) {
            endDate.setCustomValidity('End date must be after start date');
        } else {
            endDate.setCustomValidity('');
        }
    }

    startDate.addEventListener('change', validateDates);
    endDate.addEventListener('change', validateDates);

    // Validate deadlines are within the timeframe period
    const proposalDeadline = document.getElementById('proposal_submission_deadline');
    const supervisorDeadline = document.getElementById('supervisor_confirmation_deadline');

    function validateDeadlines() {
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        const proposal = new Date(proposalDeadline.value);
        const supervisor = new Date(supervisorDeadline.value);

        if (proposal < start || proposal > end) {
            proposalDeadline.setCustomValidity('Deadline must be within the timeframe period');
        } else {
            proposalDeadline.setCustomValidity('');
        }

        if (supervisor < start || supervisor > end) {
            supervisorDeadline.setCustomValidity('Deadline must be within the timeframe period');
        } else {
            supervisorDeadline.setCustomValidity('');
        }

        if (supervisor < proposal) {
            supervisorDeadline.setCustomValidity('Supervisor confirmation deadline must be after proposal submission deadline');
        }
    }

    proposalDeadline.addEventListener('change', validateDeadlines);
    supervisorDeadline.addEventListener('change', validateDeadlines);
});
</script>
@endpush

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush
