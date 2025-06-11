@extends('layouts.dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            <i class="fas fa-file-alt text-blue-500 mr-2"></i>
                            Manage Proposals
                        </h1>
                    </div>

                    @php
                        $activeTimeframe = App\Models\Timeframe::where('start_date', '<=', now())
                            ->where('end_date', '>=', now())
                            ->where('status', 'active')
                            ->first();

                        $proposalSubmissionOpen = false;
                        if ($activeTimeframe) {
                            $proposalSubmissionOpen = now()->lessThan($activeTimeframe->proposal_submission_deadline);
                        }

                        // Debug information
                        \Log::info('Timeframe Check:', [
                            'active_timeframe' => $activeTimeframe ? [
                                'id' => $activeTimeframe->id,
                                'start_date' => $activeTimeframe->start_date,
                                'end_date' => $activeTimeframe->end_date,
                                'submission_deadline' => $activeTimeframe->proposal_submission_deadline,
                            ] : 'No active timeframe',
                            'current_time' => now(),
                            'submission_open' => $proposalSubmissionOpen
                        ]);
                    @endphp

                    <!-- Add New Proposal Button -->
                    @if($proposalSubmissionOpen)
                        <a href="{{ route('lecturer.proposals.create', ['user_id' => Auth::id()]) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-plus mr-2"></i>
                            Add New Proposal
                        </a>
                    @else
                        <button disabled
                            class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest cursor-not-allowed">
                            <i class="fas fa-lock mr-2"></i>
                            Proposal Submission Closed
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Proposals List -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                @if($proposals->isEmpty())
                    <div class="text-center py-8">
                        <i class="fas fa-folder-open text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900">No Proposals Yet</h3>
                        <p class="text-gray-500 mt-1">Start by creating your first project proposal</p>
                        @if($proposalSubmissionOpen)
                            <a href="{{ route('lecturer.proposals.create', ['user_id' => Auth::id()]) }}"
                                class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                <i class="fas fa-plus mr-2"></i>
                                Create Proposal
                            </a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Max Students</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Timeframe</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($proposals as $proposal)
                                    <!-- Proposal -->
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $proposal->title }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500">{{ Str::limit($proposal->description, 100) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @php
                                                $approvedCount = $proposals->where('status', 'approved')->count();
                                                $availableQuota = $lecturer->max_students - $approvedCount;
                                            @endphp
                                            <div class="text-sm text-gray-900">
                                                {{ $availableQuota }} / {{ $lecturer->max_students }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($proposal->timeframe)
                                                <span class="text-sm text-gray-900">
                                                    {{ $proposal->timeframe->semester }} {{ $proposal->timeframe->academic_year }}
                                                </span>
                                            @else
                                                <span class="text-sm text-red-500">No timeframe set</span>
                                            @endif
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($proposal->status == 'available') bg-blue-100 text-blue-800
                                                @elseif($proposal->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($proposal->status == 'approved') bg-green-100 text-green-800
                                                @elseif($proposal->status == 'rejected') bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($proposal->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-3">
                                                <a href="{{ route('lecturer.proposals.show', ['user_id' => Auth::id(), 'proposal' => $proposal->id]) }}"
                                                    class="text-gray-600 hover:text-gray-900 transition-colors">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <!-- Edit Proposal -->
                                                @if($proposalSubmissionOpen)
                                                    <a href="{{ route('lecturer.proposals.edit', ['user_id' => Auth::id(), 'proposal' => $proposal->id]) }}"
                                                        class="text-blue-600 hover:text-blue-900 transition-colors">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <!-- Delete Proposal -->
                                                    <form action="{{ route('lecturer.proposals.destroy', ['user_id' => Auth::id(), 'proposal' => $proposal->id]) }}"
                                                        method="POST" class="inline-block"
                                                        onsubmit="return confirm('Are you sure you want to delete this proposal?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 transition-colors">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush
@endsection
