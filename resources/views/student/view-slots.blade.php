@extends('layouts.dashboard')

@section('title', 'Available Appointment Slots')

@section('content')
    <div class="py-12" x-data="{
        showModal: false,
        appointmentId: null,
        meetingType: 'online',
        showMeetingLink: false
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-blue-600">Available Supervision Slots</h1>
                        <p class="text-gray-600 mt-1">{{ Auth::user()->name }}'s Available Supervision Slots</p>
                    </div>
                    <a href="{{ route('student.dashboard', ['user_id' => $user_id]) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>

                {{-- <!-- Add this after the header section -->
            @if (config('app.debug'))
                <div class="mb-4 p-4 bg-gray-100 rounded">
                    <h3 class="font-semibold text-gray-700">Debug Information:</h3>
                    <pre class="mt-2 text-sm">
                        Appointments Count: {{ $appointments->count() }}
                        Lecturer IDs: {{ implode(', ', $appointments->keys()->toArray()) }}
                        
                        Appointments Data:
                        @foreach ($appointments as $lecturerId => $lecturerAppointments)
                            Lecturer ID: {{ $lecturerId }}
                            Appointments in group: {{ $lecturerAppointments->count() }}
                            First Appointment:
                            @if ($lecturerAppointments->first())
                                Date: {{ $lecturerAppointments->first()->appointment_date }}
                                Raw Lecturer ID: {{ $lecturerAppointments->first()->lecturer_id }}
                                Lecturer User ID: {{ optional($lecturerAppointments->first()->lecturer)->user_id }}
                                Lecturer exists: {{ $lecturerAppointments->first()->lecturer ? 'Yes' : 'No' }}
                                User exists: {{ optional($lecturerAppointments->first()->lecturer)->user ? 'Yes' : 'No' }}
                                User Name: {{ optional(optional($lecturerAppointments->first()->lecturer)->user)->name }}
                            @endif
                            ----------------------
                        @endforeach

                        Raw Appointments:
                        @foreach ($debug_raw_appointments as $appointment)
                            ID: {{ $appointment->id }}
                            Lecturer ID: {{ $appointment->lecturer_id }}
                            Date: {{ $appointment->appointment_date }}
                            Time: {{ $appointment->start_time }} - {{ $appointment->end_time }}
                            Has Lecturer: {{ $appointment->lecturer ? 'Yes' : 'No' }}
                            Lecturer Name: {{ optional(optional($appointment->lecturer)->user)->name }}
                            ----------------------
                        @endforeach
                    </pre>
                </div>
            @endif --}}

                <!-- Alert Messages -->
                @if (session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative"
                        role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Appointments List -->
                @if ($appointments && $appointments->count() > 0)
                    @foreach ($appointments as $lecturerId => $lecturerAppointments)
                        @php
                            $lecturer = $lecturerAppointments->first()->lecturer ?? null;
                        @endphp
                        @if ($lecturer && $lecturer->user)
                            <div class="bg-gray-50 rounded-xl p-6 mb-6">
                                <div class="flex items-center mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800">{{ $lecturer->user->name }}</h3>
                                        <p class="text-sm text-gray-600">Research Group:
                                            {{ $lecturer->research_group ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($lecturerAppointments as $appointment)
                                        <div
                                            class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="font-medium text-gray-900">
                                                        {{ Carbon\Carbon::parse($appointment->appointment_date)->format('l, d F Y') }}
                                                    </p>
                                                    <p class="text-gray-600 mt-1">
                                                        <i class="fas fa-clock text-blue-500 mr-2"></i>
                                                        {{ Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }}
                                                        -
                                                        {{ Carbon\Carbon::parse($appointment->end_time)->format('g:i A') }}
                                                    </p>
                                                </div>
                                                <button @click="showModal = true; appointmentId = {{ $appointment->id }}"
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-1.5 bg-blue-50 border border-blue-100 rounded-lg text-blue-600 text-sm hover:bg-blue-100 transition-colors duration-200">
                                                    <i class="fas fa-calendar-check mr-1.5"></i>
                                                    Book
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                            <i class="fas fa-calendar-times text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">No Available Slots</h3>
                        <p class="mt-2 text-gray-500">Check back later for new appointment slots.</p>
                    </div>
                @endif
            </div>

            <!-- Booking Modal -->
            <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"
                        aria-hidden="true"></div>

                    <!-- Modal panel -->
                    <div x-show="showModal" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form method="POST"
                            x-bind:action="'{{ url('student/' . $user_id . '/appointments') }}/' + appointmentId + '/book'">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Book Appointment</h3>

                                <div class="space-y-4">
                                    <!-- Title -->
                                    <div>
                                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                        <input type="text" name="title" id="title" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <label for="description"
                                            class="block text-sm font-medium text-gray-700">Description</label>
                                        <textarea name="description" id="description" required rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                    </div>

                                    <!-- Meeting Type -->
                                    <div>
                                        <label for="meeting_type" class="block text-sm font-medium text-gray-700">Meeting
                                            Type</label>
                                        <select name="meeting_type" id="meeting_type" x-model="meetingType"
                                            @change="showMeetingLink = (meetingType === 'online')"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="online">Online (Google Meet)</option>
                                            <option value="in-person">In Person</option>
                                        </select>
                                    </div>

                                    <!-- Meeting Link (Optional, shown only for online meetings) -->
                                    <div x-show="showMeetingLink">
                                        <label for="meeting_link" class="block text-sm font-medium text-gray-700">Meeting
                                            Link (Optional)</label>
                                        <input type="url" name="meeting_link" id="meeting_link"
                                            placeholder="https://meet.google.com/..."
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Book Appointment
                                </button>
                                <button type="button" @click="showModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('appointmentModal', () => ({
                showModal: false,
                appointmentId: null,
                meetingType: 'online',
                showMeetingLink: false
            }))
        })
    </script>
@endpush
