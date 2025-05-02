@extends('layouts.dashboard')

@section('title', 'Browse Proposals')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Browse Proposals</h1>

        <!-- Search and Filter Section -->
        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
            <form action="{{ route('student.browse-proposals', ['user_id' => auth()->id()]) }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Search by title or description">
                </div>
                <div>
                    <label for="lecturer" class="block text-sm font-medium text-gray-700">Lecturer</label>
                    <select name="lecturer" id="lecturer"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Lecturers</option>
                        @foreach ($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}"
                                {{ request('lecturer') == $lecturer->id ? 'selected' : '' }}>
                                {{ $lecturer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available
                        </option>
                        <option value="taken" {{ request('status') == 'taken' ? 'selected' : '' }}>Taken</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Filter Results
                    </button>
                </div>
            </form>
        </div>

        <!-- Proposals List -->
        <div class="space-y-6">
            @forelse($proposals as $proposal)
                <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $proposal->title }}</h3>
                            <p class="text-sm text-gray-600">Proposed by: {{ optional($proposal->lecturer)->name ?? 'Unknown Lecturer' }}</p>
                        </div>
                        <span
                            class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $proposal->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($proposal->status) }}
                        </span>
                    </div>
                    <p class="mt-2 text-gray-600">{{ $proposal->description }}</p>
                    <div class="mt-4 flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            <span class="mr-4"><i class="fas fa-clock mr-1"></i>Posted:
                                {{ $proposal->created_at->diffForHumans() }}</span>
                            <span><i class="fas fa-tag mr-1"></i>{{ $proposal->field_of_study }}</span>
                        </div>
                        @if ($proposal->status === 'available')
                            <button type="button"
                                onclick="openApplicationModal({{ json_encode([
                                    'id' => $proposal->id,
                                    'title' => $proposal->title,
                                    'lecturer_id' => $proposal->lecturer_id,
                                    'lecturer_name' => optional($proposal->lecturer)->name ?? 'Unknown Lecturer',
                                    'description' => $proposal->description
                                ]) }})"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Apply Now
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <p>No proposals found matching your criteria.</p>
                </div>
            @endforelse

            <!-- Pagination -->
            <div class="mt-6">
                {{ $proposals->links() }}
            </div>
        </div>
    </div>

    <!-- Application Modal -->
    <div id="applicationModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Apply for Project Proposal</h3>

                    <form action="{{ route('student.update-proposal', ['user_id' => Auth::id()]) }}" method="POST" id="applicationForm">
                        @csrf

                        <input type="hidden" name="proposal_id" id="proposal_id">
                        <input type="hidden" name="lecturer_id" id="lecturer_id">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Project Title</label>
                                <p id="modal_title" class="mt-1 text-sm text-gray-900"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Supervisor</label>
                                <p id="modal_lecturer" class="mt-1 text-sm text-gray-900"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Project Description</label>
                                <p id="modal_description" class="mt-1 text-sm text-gray-600"></p>
                            </div>
                        </div>

                        <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Submit Application
                            </button>
                            <button type="button" onclick="closeApplicationModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            <p class="font-bold">Error!</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if(session('success'))
        <div class="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
            <p class="font-bold">Success!</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @push('scripts')
    <script>
        function openApplicationModal(proposal) {
            document.getElementById('proposal_id').value = proposal.id;
            document.getElementById('lecturer_id').value = proposal.lecturer_id;
            document.getElementById('modal_title').textContent = proposal.title;
            document.getElementById('modal_lecturer').textContent = proposal.lecturer_name;
            document.getElementById('modal_description').textContent = proposal.description;
            document.getElementById('applicationModal').classList.remove('hidden');
        }

        function closeApplicationModal() {
            document.getElementById('applicationModal').classList.add('hidden');
            document.getElementById('applicationForm').reset();
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.display = 'none';
            });
        }, 5000);
    </script>
    @endpush
@endsection
