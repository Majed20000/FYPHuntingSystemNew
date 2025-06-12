@extends('layouts.dashboard') <!-- Extend the dashboard layout -->

@section('title', 'User Management') <!-- The title of the page -->

@section('content') <!-- Start of the content of the page -->
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
            <div class="flex gap-2">
                <a href="{{ route('coordinator.users.export.excel') }}"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </a>
                <a href="{{ route('coordinator.users.export.pdf') }}"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>

        <!-- Search and Filter Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form action="{{ route('coordinator.userRegister') }}" method="GET" class="flex gap-4">
                <!-- Search and filter the users -->
                <div class="flex-1"> <!-- The input field to search by name, email, or matric number -->
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by name, email, or matric number"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="w-48"> <!-- The dropdown menu to select by roles -->
                    <select name="role"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Roles</option> <!-- Select by roles -->
                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="lecturer" {{ request('role') === 'lecturer' ? 'selected' : '' }}>Lecturer</option>
                        <option value="coordinator" {{ request('role') === 'coordinator' ? 'selected' : '' }}>Coordinator
                        </option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Search
                </button>
            </form>
        </div>

        <!-- CSV Upload Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Upload Users</h2>
                <button onclick="window.location.href='{{ asset('templates/user_registration_template.csv') }}'"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Download CSV Template
                </button> <!-- Button to download the CSV template fetched from the assets folder -->
            </div>
            <form action="{{ route('coordinator.register.store') }}" method="POST" enctype="multipart/form-data">
                <!-- Form to upload the CSV file -->
                @csrf
                <div class="flex gap-4">
                    <div class="flex-1">
                        <input type="file" name="csv_file" accept=".csv"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <!-- Box that show name of the file -->
                    </div>

                    <!-- Button to upload the CSV file -->
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Upload CSV
                    </button>
                </div>
            </form>
        </div>

        <!-- Messages -->
        @if (session()->has('success'))
            <!-- Display the success message of uploading the CSV file -->
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <!-- Display the error message of uploading the CSV file -->
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('failures'))
            <!-- Display the error message of registering the users -->
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">
                {{ session('failures') }}
                @if (session()->has('errors'))
                    <ul class="list-disc list-inside mt-2">
                        @foreach (session('errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Number
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($users as $user)
                        <!-- Display the users, fetched from the table user -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-sm rounded-full capitalize
                                    @if($user->role === 'student')
                                        bg-blue-100 text-blue-800
                                    @elseif($user->role === 'lecturer')
                                        bg-green-100 text-green-800
                                    @elseif($user->role === 'coordinator')
                                        bg-purple-100 text-purple-800
                                    @endif">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->matric_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button onclick="deleteUser({{ $user->id }})" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button> <!-- Button to delete the user -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <!-- Pagination Links -->
        <div class="mt-4">
            @if ($users->hasPages())
                <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center">
                    {{-- Previous Page Link --}}
                    @if ($users->onFirstPage())
                        <span
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-l-md">
                            Previous
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" rel="prev"
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50">
                            Previous
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    <div class="hidden md:inline-flex">
                        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                            @if ($page == $users->currentPage())
                                <span
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-gray-300">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    </div>

                    {{-- Next Page Link --}}
                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" rel="next"
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <span
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-r-md">
                            Next
                        </span>
                    @endif
                </nav>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // Function to delete the user
            function deleteUser(userId) {
                if (confirm('Are you sure you want to delete this user?')) {
                    fetch(`/coordinator/register/${userId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error deleting user');
                        });
                }
            }
        </script>
    @endpush
@endsection
