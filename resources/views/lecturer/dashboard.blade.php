@extends('layouts.dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Add the countdown component -->
        @include('components.timeframe-countdown')

        <!-- Rest of your dashboard content -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="container mx-auto px-4 py-6 bg-white">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center space-x-4">
                        <h1 class="text-3xl font-bold text-blue-600">Dashboard</h1>
                        <div class="h-8 w-1 bg-gradient-to-b from-blue-400 to-purple-400 rounded-full"></div>
                    </div>
                    <a href="{{ route('lecturer.calendar', ['user_id' => $user_id]) }}"
                       class="inline-flex items-center px-6 py-2.5 border border-blue-100 text-sm font-medium rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors duration-200 shadow-sm">
                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                        View Calendar
                    </a>
                </div>

                <!-- Supervision Quota Card with Progress Bar -->
                <div class="mb-6 p-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-100">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-blue-700">Supervision Quota</h2>
                        <div class="text-sm text-gray-600">
                            <span class="font-semibold text-blue-600">{{ Auth::user()->lecturer->current_students }}</span>
                            <span class="mx-1">/</span>
                            <span class="font-semibold text-purple-600">{{ Auth::user()->lecturer->max_students }}</span>
                            <span class="ml-1">students</span>
                        </div>
                    </div>

                    @php
                        $percentage = (Auth::user()->lecturer->current_students / Auth::user()->lecturer->max_students) * 100;
                        $availableSlots = Auth::user()->lecturer->max_students - Auth::user()->lecturer->current_students;

                        // Determine progress bar color based on percentage
                        if ($percentage >= 90) {
                            $progressColor = 'bg-red-500';
                            $bgColor = 'bg-red-100';
                        } elseif ($percentage >= 70) {
                            $progressColor = 'bg-yellow-500';
                            $bgColor = 'bg-yellow-100';
                        } else {
                            $progressColor = 'bg-green-500';
                            $bgColor = 'bg-green-100';
                        }
                    @endphp

                    <!-- Main Progress Bar -->
                    <div class="relative pt-1">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                                    Progress
                                </span>
                            </div>

                        </div>
                        <div class="flex h-3 mb-4 relative">
                            <div class="w-full {{ $bgColor }} rounded-full">
                                <div style="width: {{ $percentage }}%"
                                     class="h-3 rounded-full {{ $progressColor }} transition-all duration-500 ease-in-out">
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Current Load</span>
                                    <span class="text-sm font-bold text-blue-600">
                                        {{ Auth::user()->lecturer->current_students }} Students
                                    </span>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Available Slots</span>
                                    <span class="text-sm font-bold {{ $availableSlots > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $availableSlots }} Slots
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Quick Actions Card -->
                    <div class="p-6 bg-blue-50 rounded-xl border border-blue-100">
                        <h2 class="text-lg font-semibold text-blue-700 mb-4">Quick Actions</h2>
                        <div class="space-y-3">
                            <a href="{{ route('lecturer.proposals.create', ['user_id' => $user_id]) }}"
                               class="block w-full p-3 bg-white rounded-lg border border-blue-200 hover:bg-blue-50 transition-colors duration-200">
                                <i class="fas fa-plus-circle mr-2 text-blue-500"></i>
                                Create New Proposal
                            </a>
                            <!-- Manage Proposals -->
                            <a href="{{ route('lecturer.proposals.manage', ['user_id' => $user_id]) }}"
                               class="block w-full p-3 bg-white rounded-lg border border-blue-200 hover:bg-blue-50 transition-colors duration-200">
                                <i class="fas fa-tasks mr-2 text-blue-500"></i>
                                Manage Proposals
                            </a>
                            <!-- View Applications -->
                            <a href="{{ route('lecturer.applications.manage', ['user_id' => $user_id]) }}"
                               class="block w-full p-3 bg-white rounded-lg border border-blue-200 hover:bg-blue-50 transition-colors duration-200">
                                <i class="fas fa-user-graduate mr-2 text-blue-500"></i>
                                View Applications
                            </a>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
