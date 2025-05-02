@extends('layouts.dashboard')

@section('title', 'Timeframes')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Timeframes</h1>
            <p class="text-gray-600 mt-1">Manage academic timeframes</p>
        </div>
        <div class="flex space-x-4">
            @if($timeframes->where('is_active', true)->first())
                <a href="{{ route('coordinator.timeframes.quotas.manage', ['timeframe' => $timeframes->where('is_active', true)->first()->id]) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-users-cog mr-2"></i>
                    Manage Lecturer Quotas
                </a>
            @endif
            <a href="{{ route('coordinator.timeframes.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>
                Add Timeframe
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Academic Year
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Semester
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Period
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($timeframes as $timeframe)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $timeframe->academic_year }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            Semester {{ $timeframe->semester }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $timeframe->start_date->format('d/m/Y') }} - {{ $timeframe->end_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $timeframe->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $timeframe->status }}
                                {{ $timeframe->is_active ? '(Active)' : '' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                @if($timeframe->is_active)
                                    <a href="{{ route('coordinator.timeframes.quotas.manage', ['timeframe' => $timeframe->id]) }}" 
                                       class="text-indigo-600 hover:text-indigo-900"
                                       title="Manage Lecturer Quotas">
                                        <i class="fas fa-users-cog"></i>
                                    </a>
                                @endif
                                <a href="{{ route('coordinator.timeframes.edit', $timeframe->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                                @if(!$timeframe->is_active)
                                    <form action="{{ route('coordinator.timeframes.destroy', $timeframe->id) }}" 
                                          method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure you want to delete this timeframe?')">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No timeframes found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush 