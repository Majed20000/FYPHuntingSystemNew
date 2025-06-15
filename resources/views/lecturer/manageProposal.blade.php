@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">
            <i class="fas fa-file-alt text-blue-600 mr-3"></i>
            Manage Proposals
        </h1>

        @php
            $activeTimeframe = App\Models\Timeframe::where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where('status', 'active')
                ->first();

            $proposalSubmissionOpen = false;
            if ($activeTimeframe) {
                $proposalSubmissionOpen = now()->lessThan($activeTimeframe->proposal_submission_deadline);
            }
        @endphp

        <!-- Add New Proposal Button -->
        @if($proposalSubmissionOpen)
            <a href="{{ route('lecturer.proposals.create', ['user_id' => Auth::id()]) }}"
                class="inline-flex items-center px-5 py-2.5 bg-blue-700 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 transition duration-300 ease-in-out transform hover:scale-105 shadow-md">
                <i class="fas fa-plus mr-2"></i>
                Add New Proposal
            </a>
        @else
            <button disabled
                class="inline-flex items-center px-5 py-2.5 bg-gray-400 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest cursor-not-allowed shadow-sm">
                <i class="fas fa-lock mr-2"></i>
                Submission Closed
            </button>
        @endif
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6 shadow-sm" role="alert">
            <span class="block sm:inline"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6 shadow-sm" role="alert">
            <span class="block sm:inline"><i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Proposals List -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <div class="p-6 bg-white border-b border-gray-200">
            @if($proposals->isEmpty())
                <div class="text-center py-10">
                    <i class="fas fa-folder-open text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Proposals Yet</h3>
                    <p class="text-gray-600 mt-1 mb-4">Start by creating your first project proposal. It will appear here for management.</p>
                    @if($proposalSubmissionOpen)
                        <a href="{{ route('lecturer.proposals.create', ['user_id' => Auth::id()]) }}"
                            class="inline-flex items-center px-6 py-2.5 bg-blue-700 text-white rounded-md hover:bg-blue-800 transition duration-300 ease-in-out transform hover:scale-105 shadow-md">
                            <i class="fas fa-plus mr-2"></i>
                            Create New Proposal
                        </a>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Students (Current/Max)</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Timeframe</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($proposals as $proposal)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $proposal->title }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600">{{ Str::limit($proposal->description, 80) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            // Correct way to count approved students for *this lecturer*
                                            $approvedStudents = App\Models\ProjectProposal::where('lecturer_id', Auth::user()->lecturer->id)
                                                                ->where('status', 'approved')
                                                                ->count();
                                            $maxStudents = Auth::user()->lecturer->max_students;
                                        @endphp
                                        <div class="text-sm font-semibold text-gray-900">
                                            <span class="{{ $approvedStudents >= $maxStudents ? 'text-red-600' : 'text-green-600' }}">
                                                {{ $approvedStudents }}
                                            </span> / {{ $maxStudents }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($proposal->timeframe)
                                            <span class="text-sm font-medium text-gray-800">
                                                {{ $proposal->timeframe->semester }} ({{ $proposal->timeframe->academic_year }})
                                            </span>
                                        @else
                                            <span class="text-sm text-red-500 italic">No timeframe set</span>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($proposal->status == 'available') bg-blue-100 text-blue-800
                                            @elseif($proposal->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($proposal->status == 'approved') bg-green-100 text-green-800
                                            @elseif($proposal->status == 'rejected') bg-red-100 text-red-800
                                            @elseif($proposal->status == 'unavailable') bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($proposal->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <a href="{{ route('lecturer.proposals.show', ['user_id' => Auth::id(), 'proposal' => $proposal->id]) }}"
                                                class="text-gray-600 hover:text-blue-600 transition-colors duration-150" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($proposalSubmissionOpen)
                                                <a href="{{ route('lecturer.proposals.edit', ['user_id' => Auth::id(), 'proposal' => $proposal->id]) }}"
                                                    class="text-blue-600 hover:text-indigo-600 transition-colors duration-150" title="Edit Proposal">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="{{ route('lecturer.proposals.destroy', ['user_id' => Auth::id(), 'proposal' => $proposal->id]) }}"
                                                    method="POST" class="inline-block"
                                                    onsubmit="return confirm('Are you sure you want to delete this proposal? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 transition-colors duration-150" title="Delete Proposal">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 cursor-not-allowed" title="Editing is closed"><i class="fas fa-edit"></i></span>
                                                <span class="text-gray-400 cursor-not-allowed" title="Deletion is closed"><i class="fas fa-trash-alt"></i></span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-8 px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-center">
                    {{ $proposals->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush
@endsection
