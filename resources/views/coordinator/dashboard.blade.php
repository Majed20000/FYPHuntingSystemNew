@extends('layouts.dashboard')

@section('title', 'Coordinator Dashboard')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Coordinator Dashboard</h1>
            <p class="text-gray-600 mt-1">Manage FYP supervision system</p>
        </div>
        <div class="flex space-x-4">
            <a href="{{ route('coordinator.userRegister') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-user-plus mr-2"></i>
                Register Users
            </a>
            <a href="{{ route('coordinator.timeframes.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-calendar-plus mr-2"></i>
                Add Timeframe
            </a>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
        <!-- Quick Stats Cards -->
        <div class="bg-blue-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-700">Active Timeframe</h3>
            <p class="text-sm text-blue-600 mt-2">Current academic period and deadlines</p>
            <a href="{{ route('coordinator.timeframes.index') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-4 inline-block">
                View All Timeframes →
            </a>
        </div>

        <div class="bg-green-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-green-700">User Management</h3>
            <p class="text-sm text-green-600 mt-2">Manage students and lecturers</p>
            <a href="{{ route('coordinator.userRegister') }}" class="text-green-600 hover:text-green-800 text-sm mt-4 inline-block">
                Manage Users →
            </a>
        </div>

        <div class="bg-purple-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-purple-700">Supervision Status</h3>
            <p class="text-sm text-purple-600 mt-2">Overview of supervision assignments</p>
            <a href="#" class="text-purple-600 hover:text-purple-800 text-sm mt-4 inline-block">
                View Status →
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush