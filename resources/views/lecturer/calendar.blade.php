@extends('layouts.dashboard')

@section('title', 'Manage Appointment Slots')

@section('content')
<div class="py-12" x-data="{ 
    showModal: false,
    showReviewModal: false,
    showRejectModal: false,
    currentAppointment: null
}">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Alert Messages -->
        @if (session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center space-x-4">
                    <h1 class="text-3xl font-bold text-blue-600">{{ Auth::user()->name }}'s Appointment Slots</h1>
                    <div class="h-8 w-1 bg-gradient-to-b from-blue-400 to-purple-400 rounded-full"></div>
                </div>
                <div x-data="{ showModal: false }">
                    <button @click="showModal = true" 
                            class="inline-flex items-center px-6 py-2.5 bg-blue-50 border border-blue-100 rounded-lg text-blue-600 hover:bg-blue-100 transition-colors duration-200 shadow-sm">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Slot
                    </button>

                    <!-- Modal -->
                    <div x-show="showModal" 
                         class="fixed inset-0 z-50 overflow-y-auto" 
                         aria-labelledby="modal-title" 
                         role="dialog" 
                         aria-modal="true">
                        <!-- Background backdrop -->
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                        <div class="fixed inset-0 z-10 overflow-y-auto">
                            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                <div x-show="showModal"
                                     x-transition:enter="ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                     x-transition:leave="ease-in duration-200"
                                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                                    
                                    <div class="absolute right-0 top-0 hidden pr-4 pt-4 sm:block">
                                        <button @click="showModal = false" type="button" class="text-gray-400 hover:text-gray-500">
                                            <span class="sr-only">Close</span>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <div class="sm:flex sm:items-start">
                                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                            <h3 class="text-xl font-semibold leading-6 text-gray-900" id="modal-title">
                                                Add New Appointment Slot
                                            </h3>
                                            <div class="mt-4">
                                                <form action="{{ route('lecturer.appointments.store', ['user_id' => $user_id]) }}" 
                                                      method="POST"
                                                      x-data="{ 
                                                          startTime: '',
                                                          endTime: '',
                                                          validateTimes() {
                                                              if (this.startTime && this.endTime && this.startTime >= this.endTime) {
                                                                  alert('End time must be after start time');
                                                                  this.endTime = '';
                                                              }
                                                          }
                                                      }">
                                                    @csrf
                                                    <div class="space-y-4">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Date</label>
                                                            <input type="date" 
                                                                   name="appointment_date" 
                                                                   required
                                                                   min="{{ date('Y-m-d') }}"
                                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                            @error('appointment_date')
                                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Start Time</label>
                                                            <input type="time" 
                                                                   name="start_time"
                                                                   x-model="startTime"
                                                                   @change="validateTimes()"
                                                                   required
                                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                            @error('start_time')
                                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">End Time</label>
                                                            <input type="time"
                                                                   name="end_time"
                                                                   x-model="endTime"
                                                                   @change="validateTimes()"
                                                                   required 
                                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                            @error('end_time')
                                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                                        <button type="submit"
                                                                class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:col-start-2">
                                                            Add Slot
                                                        </button>
                                                        <button type="button"
                                                                @click="showModal = false"
                                                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointment Slots List -->
            <div class="space-y-6">
                <!-- Group appointments by date -->
                @php
                    $groupedAppointments = $appointments->groupBy(function($appointment) {
                        return $appointment->appointment_date->format('Y-m-d');
                    });
                @endphp

                @forelse($groupedAppointments as $date => $dateAppointments)
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($dateAppointments as $appointment)
                                <div class="bg-white rounded-lg border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-clock text-blue-500"></i>
                                            <div>
                                                <p class="font-medium text-gray-900">
                                                    {{ $appointment->start_time->format('h:i A') }} - {{ $appointment->end_time->format('h:i A') }}
                                                </p>
                                                <div class="flex items-center space-x-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        {{ $appointment->status === 'available' ? 'bg-green-100 text-green-800' : 
                                                           ($appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                    
                                                    <!-- Add Review Button for pending appointments -->
                                                    @if($appointment->status === 'pending')
                                                        <button 
                                                            @click="showReviewModal = true; currentAppointment = {{ $appointment->id }}"
                                                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-700 bg-blue-50 rounded-md hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                            <i class="fas fa-eye mr-1"></i>
                                                            Review
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @if($appointment->status === 'available')
                                            <form action="{{ route('lecturer.appointments.delete', ['user_id' => $user_id, 'id' => $appointment->id]) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this slot?');"
                                                  class="ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-400 hover:text-red-600 transition-colors duration-200">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    @if($appointment->status !== 'available')
                                        <div class="mt-3 pt-3 border-t border-gray-100">
                                            <div class="space-y-2">
                                                <div class="flex items-center text-gray-600">
                                                    <i class="fas fa-user w-5 mr-2 text-gray-400"></i>
                                                    <span>{{ $appointment->student->user->name ?? 'N/A' }}</span>
                                                </div>
                                                @if($appointment->title)
                                                    <div class="flex items-center text-gray-600">
                                                        <i class="fas fa-file-alt w-5 mr-2 text-gray-400"></i>
                                                        <span>{{ $appointment->title }}</span>
                                                    </div>
                                                @endif
                                                @if($appointment->meeting_type)
                                                    <div class="flex items-center text-gray-600">
                                                        <i class="fas {{ $appointment->meeting_type === 'online' ? 'fa-video' : 'fa-building' }} w-5 mr-2 text-gray-400"></i>
                                                        <span>{{ ucfirst($appointment->meeting_type) }}</span>
                                                    </div>
                                                @endif
                                                @if($appointment->meeting_link)
                                                    <div class="flex items-center text-gray-600">
                                                        <i class="fas fa-link w-5 mr-2 text-gray-400"></i>
                                                        <a href="{{ $appointment->meeting_link }}" target="_blank" class="text-blue-600 hover:underline">Meeting Link</a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 mb-4">
                            <i class="fas fa-calendar-plus text-2xl text-blue-500"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">No appointment slots</h3>
                        <p class="mt-2 text-gray-500">Get started by adding your first appointment slot.</p>
                        
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div x-show="showReviewModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showReviewModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 @click="showReviewModal = false"
                 aria-hidden="true"></div>

            <!-- Modal panel -->
            <div x-show="showReviewModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Review Appointment Request</h3>
                    <div class="space-y-4">
                        @foreach($appointments as $appt)
                            <template x-if="currentAppointment === {{ $appt->id }}">
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium">Student:</span>
                                        <span>{{ $appt->student->user->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium">Title:</span>
                                        <span>{{ $appt->title }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium">Description:</span>
                                        <span>{{ $appt->description }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium">Meeting Type:</span>
                                        <span>{{ ucfirst($appt->meeting_type) }}</span>
                                    </div>
                                    @if($appt->meeting_type === 'online' && $appt->meeting_link)
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium">Meeting Link:</span>
                                            <a href="{{ $appt->meeting_link }}" target="_blank" class="text-blue-600 hover:underline">
                                                {{ $appt->meeting_link }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </template>
                        @endforeach
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button @click="showReviewModal = false"
                                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                Close
                            </button>
                            
                            <form x-bind:action="'{{ url('lecturer/' . $user_id . '/appointments') }}/' + currentAppointment + '/accept'"
                                  method="POST"
                                  class="inline">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700">
                                    Accept
                                </button>
                            </form>

                            <button @click="showRejectModal = true; showReviewModal = false"
                                    class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700">
                                Reject
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Reject Modal -->
    <div x-show="showRejectModal" 
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 @click="showRejectModal = false"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form x-bind:action="'{{ url('lecturer/' . $user_id . '/appointments') }}/' + currentAppointment + '/reject'"
                      method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Appointment</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Reason for Rejection</label>
                                <textarea name="rejection_reason" 
                                        id="rejection_reason"
                                        required
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="Please provide a reason for rejecting this appointment request"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirm Rejection
                        </button>
                        <button type="button"
                                @click="showRejectModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
