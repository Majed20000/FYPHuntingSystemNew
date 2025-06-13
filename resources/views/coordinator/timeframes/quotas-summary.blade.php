@extends('layouts.dashboard')

@section('title', 'Quota Summary')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Quota Summary</h1>
            <p class="text-gray-600 mt-1">{{ $timeframe->academic_year }} Semester {{ $timeframe->semester }}</p>
        </div>
        <a href="{{ route('coordinator.timeframes.quotas.manage', ['timeframe' => $timeframe->id]) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Manage Quotas
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Lecturer Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Assigned Students
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Remaining Slots
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($lecturers as $lecturer)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $lecturer['name'] }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if(count($lecturer['students']) > 0)
                            <ul class="list-disc list-inside text-sm text-gray-600">
                                @foreach($lecturer['students'] as $student)
                                    <li>{{ $student }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-sm text-gray-500">No students assigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $lecturer['remaining_slots'] }} / {{ $lecturer['max_students'] }}
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
