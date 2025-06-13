@extends('layouts.dashboard')

@section('title', 'Manage Lecturer Quotas')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Manage Lecturer Quotas</h1>
            <p class="text-gray-600 mt-1">Set supervision quotas for {{ $timeframe->academic_year }} Semester {{ $timeframe->semester }}</p>
        </div>
        <a href="{{ route('coordinator.timeframes.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Timeframes
        </a>
    </div>

    <div x-data="quotaManager()" x-init="initialize()">
        <!-- Default Quota Section -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4">Default Quota</h2>
            <div class="flex items-center space-x-4">
                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700">Default Value</label>
                    <input type="number"
                           x-model="defaultQuota"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           min="1"
                           max="20">
                </div>
                <button @click="applyDefaultQuota()"
                        class="mt-6 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Apply to All
                </button>
            </div>
        </div>

        <!-- Individual Quotas Section -->
        <div>
            <h2 class="text-lg font-semibold mb-4">Individual Quotas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <template x-for="lecturer in lecturers" :key="lecturer.id">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="font-medium" x-text="lecturer.name"></p>
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500">Current Students:</span>
                                <span x-text="lecturer.current_students"></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500">Max Students:</span>
                                <input type="number"
                                       x-model="lecturer.max_students"
                                       class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       min="1"
                                       max="20">
                            </div>
                            <div class="flex items-center space-x-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox"
                                           x-model="lecturer.accepting_students"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Accepting Students</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- View Summary Button -->
        <div class="mt-6">
            <a href="{{ route('coordinator.timeframes.quotas.summary', ['timeframe' => $timeframe->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                <i class="fas fa-list-ul mr-2"></i>
                View Quota Summary
            </a>
        </div>

        <!-- Save Button -->
        <div class="mt-8">
            <button @click="saveQuotas()"
                    class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700">
                Save All Changes
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function quotaManager() {
    return {
        defaultQuota: 5,
        lecturers: [],
        timeframeId: '{{ $timeframe->id }}',

        async initialize() {
            try {
                const response = await fetch(`{{ route('coordinator.timeframes.quotas.data', ['timeframe' => $timeframe->id]) }}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.lecturers = data.data.lecturers;
                    if (this.lecturers.length > 0) {
                        this.defaultQuota = this.lecturers[0].max_students;
                    }
                }
            } catch (error) {
                console.error('Error fetching quotas:', error);
            }
        },

        applyDefaultQuota() {
            if (confirm(`Are you sure you want to set all lecturers' maximum students to ${this.defaultQuota}?`)) {
                this.lecturers.forEach(lecturer => {
                    lecturer.max_students = this.defaultQuota;
                    lecturer.accepting_students = true;
                });
            }
        },

        async saveQuotas() {
            try {
                const response = await fetch(`{{ route('coordinator.timeframes.quotas.update', ['timeframe' => $timeframe->id]) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        default_quota: this.defaultQuota,
                        specific_quotas: this.lecturers.map(l => ({
                            lecturer_id: l.id,
                            max_students: l.max_students,
                            accepting_students: l.accepting_students
                        }))
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to update quotas');
                }

                const data = await response.json();
                if (data.success) {
                    alert('Lecturer settings updated successfully');
                    window.location.reload();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error saving quotas:', error);
                alert('Error saving settings: ' + error.message);
            }
        }
    }
}
</script>
@endpush