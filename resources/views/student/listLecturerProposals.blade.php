@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">
            <i class="fas fa-scroll text-blue-600 mr-3"></i>
            @if(request('lecturer_id') && $lecturer)
                {{ optional($lecturer)->name ?? 'Unknown Lecturer' }}'s Proposals
            @else
                All Proposals
            @endif
        </h1>
        @if(request('lecturer_id'))
        <button onclick="openProposalModal()"
            class="inline-flex items-center px-5 py-2.5 bg-green-700 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600 transition duration-300 ease-in-out transform hover:scale-105 shadow-md">
            <i class="fas fa-plus mr-2"></i>
            Propose New Project
        </button>
        @endif
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-8 border border-gray-200">
        <form action="{{ route('student.list-lecturer-proposals', ['user_id' => auth()->id()]) }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="search" class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-search mr-2 text-gray-500"></i>Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3"
                    placeholder="Search by title or description">
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-info-circle mr-2 text-gray-500"></i>Status</label>
                <select name="status" id="status"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full inline-flex justify-center items-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 ease-in-out transform hover:scale-105 shadow-md">
                    <i class="fas fa-filter mr-2"></i>
                    Filter Results
                </button>
            </div>
        </form>
    </div>

    <!-- Proposals List -->
    <div class="grid grid-cols-1 gap-6">
        @forelse($proposals as $proposal)
            @if($proposal->proposal_type === 'lecturer')
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $proposal->title }}</h3>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-user-tie mr-2 text-gray-500"></i>Proposed by: {{ optional($proposal->lecturer)->name ?? 'Unknown Lecturer' }}
                                </p>
                            </div>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full
                                @if($proposal->status === 'available')
                                    bg-green-100 text-green-800
                                @elseif($proposal->status === 'unavailable')
                                    bg-red-100 text-red-800
                                @else
                                    bg-yellow-100 text-yellow-800
                                @endif">
                                <i class="fas fa-info-circle mr-1"></i>{{ ucfirst($proposal->status) }}
                            </span>
                        </div>
                        <p class="text-gray-700 leading-relaxed mb-4">{{ $proposal->description }}</p>
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-flask mr-2 text-gray-500"></i><span class="font-medium text-gray-700">Field: {{ $proposal->field_of_study }}</span>
                                <span class="ml-4"><i class="fas fa-clock mr-2 text-gray-500"></i>Posted: <span class="font-medium text-gray-700">{{ $proposal->created_at->diffForHumans() }}</span></span>
                            </div>
                            @if($proposal->status === 'available')
                                @if($proposal->lecturer_id)
                                    {{-- Apply to Lecturer's Proposal --}}
                                    <button onclick="openApplicationModal({{ json_encode([
                                        'id' => $proposal->id,
                                        'title' => $proposal->title,
                                        'description' => $proposal->description,
                                        'proposer_type' => 'lecturer',
                                        'lecturer_id' => $proposal->lecturer_id,
                                        'proposer_name' => optional($proposal->lecturer)->name ?? 'Unknown Lecturer'
                                    ]) }})"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent text-sm font-medium rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 ease-in-out transform hover:scale-105 shadow-sm">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Apply Now
                                    </button>
                                @else
                                    {{-- Apply to Student's Proposal (if applicable for other students) --}}
                                    {{-- Assuming students can apply to other students' proposals for review --}}
                                    <button onclick="openApplicationModal({{ json_encode([
                                        'id' => $proposal->id,
                                        'title' => $proposal->title,
                                        'description' => $proposal->description,
                                        'proposer_type' => 'student',
                                        'student_id' => $proposal->student_id,
                                        'proposer_name' => optional($proposal->student)->name ?? 'Unknown Student'
                                    ]) }})"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent text-sm font-medium rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 ease-in-out transform hover:scale-105 shadow-sm">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Apply Now
                                    </button>
                                @endif
                            @else
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-700"><i class="fas fa-lock mr-1"></i>Not Available</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-span-full text-center py-10 bg-white rounded-xl shadow-lg border border-gray-200">
                <i class="fas fa-folder-open text-gray-400 text-6xl mb-4"></i>
                <p class="text-xl font-semibold text-gray-700 mb-2">No proposals found matching your criteria.</p>
                <p class="text-gray-500">Consider browsing lecturers or proposing a new project.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
        {{ $proposals->links() }}
    </div>
</div>

<!-- New Proposal Modal -->
<div id="proposalModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
    <div class="relative p-8 bg-white w-full max-w-md m-auto flex-col flex rounded-lg shadow-xl">
        <div class="text-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Propose New Project</h3>
            <p class="text-gray-600">Fill in the details for your new project proposal.</p>
        </div>
        <form action="{{ route('student.store-proposal', ['user_id' => auth()->id()]) }}" method="POST" id="proposalForm">
            @csrf
            <div class="space-y-4 mb-6">
                <div>
                    <label for="title" class="block text-gray-700 text-sm font-bold mb-2"><i class="fas fa-heading mr-2 text-blue-500"></i>Project Title</label>
                    <input type="text" name="title" id="title" required
                        class="appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out">
                </div>
                <div>
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2"><i class="fas fa-align-left mr-2 text-blue-500"></i>Project Description</label>
                    <textarea name="description" id="description" rows="6" required
                        class="appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out"></textarea>
                </div>
                <div>
                    <label for="field_of_study" class="block text-gray-700 text-sm font-bold mb-2"><i class="fas fa-flask mr-2 text-blue-500"></i>Field of Study</label>
                    <input type="text" name="field_of_study" id="field_of_study" required
                        class="appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out">
                </div>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeProposalModal()"
                    class="inline-flex items-center px-5 py-2.5 bg-gray-300 border border-transparent rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-300 ease-in-out shadow-sm">
                    Cancel
                </button>
                <button type="submit"
                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 ease-in-out shadow-sm">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Submit Proposal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Application Modal -->
<div id="applicationModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
    <div class="relative p-8 bg-white w-full max-w-md m-auto flex-col flex rounded-lg shadow-xl">
        <div class="text-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Apply for Project</h3>
            <p class="text-gray-600">Review the project details and confirm your application.</p>
        </div>
        <form action="" method="POST" id="applicationForm">
            @csrf
            <input type="hidden" name="proposal_id" id="proposal_id">
            <input type="hidden" name="lecturer_id" id="application_lecturer_id">
            <input type="hidden" name="student_id" id="application_student_id">

            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2"><i class="fas fa-heading mr-2 text-blue-500"></i>Project Title</label>
                    <p id="modal_title" class="mt-1 text-sm text-gray-900 font-medium"></p>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2"><i class="fas fa-user-circle mr-2 text-blue-500"></i>Proposed by</label>
                    <p id="modal_proposer" class="mt-1 text-sm text-gray-900 font-medium"></p>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2"><i class="fas fa-align-left mr-2 text-blue-500"></i>Project Description</label>
                    <p id="modal_description" class="mt-1 text-sm text-gray-700 leading-relaxed"></p>
                </div>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeApplicationModal()"
                    class="inline-flex items-center px-5 py-2.5 bg-gray-300 border border-transparent rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-300 ease-in-out shadow-sm">
                    Cancel
                </button>
                <button type="submit"
                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 ease-in-out shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    Confirm Application
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openProposalModal() {
        document.getElementById('proposalModal').classList.remove('hidden');
    }

    function closeProposalModal() {
        document.getElementById('proposalModal').classList.add('hidden');
        document.getElementById('proposalForm').reset();
    }

    function openApplicationModal(proposal) {
        document.getElementById('proposal_id').value = proposal.id;
        document.getElementById('modal_title').textContent = proposal.title;
        document.getElementById('modal_description').textContent = proposal.description;
        document.getElementById('modal_proposer').textContent = proposal.proposer_name;

        const form = document.getElementById('applicationForm');

        if (proposal.proposer_type === 'lecturer') {
            form.action = "{{ route('student.update-proposal', ['user_id' => auth()->id()]) }}";
            document.getElementById('application_lecturer_id').value = proposal.lecturer_id;
            document.getElementById('application_student_id').value = '';
        } else { // proposer_type === 'student'
            form.action = "{{ route('student.apply-proposal', ['user_id' => auth()->id()]) }}";
            document.getElementById('application_student_id').value = proposal.student_id;
            document.getElementById('application_lecturer_id').value = '';
        }

        document.getElementById('applicationModal').classList.remove('hidden');
    }

    function closeApplicationModal() {
        document.getElementById('applicationModal').classList.add('hidden');
        document.getElementById('applicationForm').reset();
    }
</script>
@endpush
@endsection 